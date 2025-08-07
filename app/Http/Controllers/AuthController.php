<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;



class AuthController extends Controller
{
    // Login user and return token
public function login(Request $request)
{
    $credentials = $request->only('username', 'password');

    if (!$accessToken = auth()->attempt($credentials)) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    // Generate "refresh" token 
    $refreshToken = JWTAuth::fromUser(auth()->user());

    return response()->json([
        'access_token' => $accessToken,
        'refresh_token' => $refreshToken,
        'user' => auth()->user()
    ]);
}


public function refreshWithToken(Request $request)
{
    try {
        $refreshToken = $request->bearerToken();

        JWTAuth::setToken($refreshToken);
        $newAccessToken = JWTAuth::refresh();

        return response()->json([
            'access_token' => $newAccessToken,
        ]);

    } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        return response()->json(['error' => 'Token has expired and can’t be refreshed'], 401);
    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        return response()->json(['error' => 'Invalid refresh token'], 401);
    }
}




    // Register Teacher (Admin only)
    public function registerTeacher(Request $request)
    {
        
        $request->headers->set('Accept', 'application/json');
        $validator = Validator::make($request->all(), [
        'username'   => 'required|min:3|unique:users,username',
        'first_name' => 'required|min:2|regex:/^[A-Za-z ]+$/',
        'last_name' => 'nullable|regex:/^[A-Za-z ]+$/',
        'email'      => 'required|email|unique:users,email',
        'password'   => 'required|min:6',
        'phone'      => 'required|digits:10',
        'employee_id'=> 'required|numeric|unique:teachers,employee_id',
        'status'     => 'required|in:active,inactive',
        'subject_specialization' => 'required|alpha',
        'date_of_joining' => 'required|date',
]);
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }
        
        // Create user
        $user = User::create([
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'teacher',
        ]);

        // Create teacher profile
        $teacher = Teacher::create([
            'user_id' => $user->id,
            'phone' => $request->phone,
            'subject_specialization' => $request->subject_specialization,
            'employee_id' => $request->employee_id,
            'date_of_joining' => $request->date_of_joining,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Teacher registered successfully',
            'teacher' => $teacher->load('user')
        ], 201);
    }

    // Register Student (Admin only)
    public function registerStudent(Request $request)
    {
        $request->headers->set('Accept', 'application/json');
         $validator = Validator::make($request->all(), [
        'username'   => 'required|min:3|unique:users,username',
        'first_name' => 'required|min:2|regex:/^[A-Za-z ]+$/',
        'last_name' => 'nullable|regex:/^[A-Za-z ]+$/',
        'email'      => 'required|email|unique:users,email',
        'password'   => 'required|min:6',
        'phone'      => 'required|digits:10',
        'roll_number' => 'required|numeric|min:1|unique:students,roll_number',
        'student_class' => 'required|numeric',
        'date_of_birth' => 'required|date',
        'admission_date' => 'required|date',
        'assigned_teacher_id' => 'nullable|exists:teachers,id',
         ]);
             if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Create User
    $user = User::create([
        'username' => $request->username,
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'student',
    ]);

    // Create Student Profile
    $student = Student::create([
        'user_id' => $user->id,
        'phone' => $request->phone,
        'roll_number' => $request->roll_number,
        'student_class' => $request->student_class,
        'date_of_birth' => $request->date_of_birth,
        'admission_date' => $request->admission_date,
        'status' => $request->status,
        'assigned_teacher_id' => $request->assigned_teacher_id ?? null,
    ]);

    return response()->json([
        'message' => 'Student registered successfully',
        'student' => $student->load('user', 'assignedTeacher.user')
    ], 201);
}
    // Get logged-in user
    public function me()
    {
        $user = auth()->user();

        // Check based on user role
        if ($user->role === 'teacher') {
            $teacher = Teacher::with('user')->where('user_id', $user->id)->first();
            return $teacher 
                ? response()->json($teacher) 
                : response()->json(['message' => 'Teacher profile not found'], 404);
        }

        if ($user->role === 'student') {
            $student = Student::with('user', 'assignedTeacher.user')
                            ->where('user_id', $user->id)->first();
            return $student 
                ? response()->json($student) 
                : response()->json(['message' => 'Student profile not found'], 404);
        }

        // Default for admin
        return response()->json($user);
    }


    // Logout
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Login user and return token
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'token' => $token,
            'user' => auth()->user()
        ]);
    }

    // Unified Register for Teacher and Student (Admin Only)
    public function register(Request $request)
    {
        // Prevent creating additional admin
        if ($request->role === 'admin') {
            return response()->json(['error' => 'Creating admin accounts is not allowed'], 403);
        }

        $request->validate([
            'username' => 'required|unique:users',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:teacher,student',

            // Teacher extra fields
            'phone' => 'required_if:role,teacher,student',
            'subject_specialization' => 'required_if:role,teacher',
            'employee_id' => 'required_if:role,teacher|unique:teachers,employee_id',
            'date_of_joining' => 'required_if:role,teacher|date',

            // Student extra fields
            'roll_number' => 'required_if:role,student|unique:students,roll_number',
            'student_class' => 'required_if:role,student',
            'date_of_birth' => 'required_if:role,student|date',
            'admission_date' => 'required_if:role,student|date',
            'assigned_teacher_id' => 'nullable|exists:teachers,id',
            'status' => 'required|in:active,inactive',
        ]);

        // Create User first
        $user = User::create([
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // If Teacher, create Teacher profile
        if ($request->role === 'teacher') {
            Teacher::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'subject_specialization' => $request->subject_specialization,
                'employee_id' => $request->employee_id,
                'date_of_joining' => $request->date_of_joining,
                'status' => $request->status,
            ]);
        }

        // If Student, create Student profile
        if ($request->role === 'student') {
            Student::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'roll_number' => $request->roll_number,
                'student_class' => $request->student_class,
                'date_of_birth' => $request->date_of_birth,
                'admission_date' => $request->admission_date,
                'status' => $request->status,
                'assigned_teacher_id' => $request->assigned_teacher_id,
            ]);
        }

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user->load(['teacher', 'student'])
        ]);
    }

    // Get logged-in user
    public function me()
    {
        return response()->json(auth()->user());
    }

    // Logout
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
}

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
        $credentials = $request->only('username', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'token' => $token,
            'user' => auth()->user()
        ]);
    }


    // Register Teacher (Admin only)
    public function registerTeacher(Request $request)
    {
        $request->headers->set('Accept', 'application/json');
        $errors =[];
        // username Validation
        if (empty($request->username)){
                $errors['username'] = "User name is required";
        }
        elseif (User::where('username', $request->username) -> exists()){
                $errors['username']= "User name already exist , try a new one";
        }
        
        // first name Validation
        if (empty($request->first_name)){
            $errors['first_name'] = " First name is required ";
        }

        elseif ( strlen($request-> first_name ) < 3) {
            $errors['first_name'] = "Name should be minimum 3 characters ";
        }
        elseif (!preg_match("/^[A-Za-z]+$/", $request->first_name)) {
            $errors['first_name'] = "First name must contain only alphabets.";
        }
        //last name Validation
        if (!empty($request->last_name)) {
            if (!preg_match("/^[A-Za-z]+$/", $request->last_name)) {
                $errors['last_name'] = "Name must contain only alphabets.";
            }
        }

        //Email Validation
        if (empty($request->email)){
            $errors['email'] = " Email is required";
        }
        
        elseif (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
        }

        elseif ( User:: where('email', $request->email)-> exists()){
            $errors['email'] = "Email must be unique";
        }

        //password Validation
        if (empty($request->password) || strlen($request->password) < 3) {
        $errors['password'] = "Password must be at least 6 characters.";
        }

         if (empty($request->phone)) {
        $errors['phone'] = "Phone is required.";
        } 
        elseif (!preg_match("/^[0-9]{10}$/", $request->phone)) {
            $errors['phone'] = "Phone must be 10 digits.";
        }

        // Employee ID Validation
        if (empty($request->employee_id)) {
            $errors['employee_id'] = "Employee ID is required.";
        } 
        elseif (Teacher::where('employee_id', $request->employee_id)->exists()) {
            $errors['employee_id'] = "Employee ID already exists.";
        }

        // Status Validation
        if (empty($request->status) || !in_array($request->status, ['active','inactive'])) {
            $errors['status'] = "Status must be either active or inactive.";
        }

        // Date Validation
        if (empty($request->date_of_joining)) {
            $errors['date_of_joining'] = "Date of joining is required.";
        }

        // Return Errors if Found
        if (!empty($errors)) {
            return response()->json(['message' => 'Validation failed', 'errors' => $errors], 422);
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
        $errors=[];

        if (empty($request->username)){
        $errors['username'] = "User name is required";
        }
        elseif (User::where('username', $request->username) -> exists()){
                $errors['username']= "User name already exist , try a new one";
        }
        
        // first name Validation
        if (empty($request->first_name)){
            $errors['first_name'] = " First name is required ";
        }

        elseif ( strlen($request-> first_name ) < 3) {
            $errors['first_name'] = "Name should be minimum 3 characters ";
        }
        elseif (!preg_match("/^[A-Za-z ]+$/", $request->first_name)) {
            $errors['first_name'] = "First name must contain only alphabets.";
        }

        if (!empty($request->last_name)) {
            if (!preg_match("/^[A-Za-z]+$/", $request->last_name)) {
                $errors['last_name'] = "Name must contain only alphabets.";
            }
        }  
        //Email Validation
        if (empty($request->email)){
            $errors['email'] = " Email is required";
        }
        
        elseif (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
        }

        elseif ( User:: where('email', $request->email)-> exists()){
            $errors['email'] = "Email must be unique";
        }

        //password Validation
        if (empty($request->password) || strlen($request->password) < 6) {
        $errors['password'] = "Password must be at least 6 characters.";
        }

        // Phone number Validation
         if (empty($request->phone)) {
        $errors['phone'] = "Phone is required.";
        } 
        elseif (!preg_match("/^[0-9]{10}$/", $request->phone)) {
            $errors['phone'] = "Phone must be 10 digits.";
        }
        // Roll Number
        if (empty($request->roll_number)) {
            $errors['roll_number'] = "Roll number is required.";
        } elseif (Student::where('roll_number', $request->roll_number)->exists()) {
            $errors['roll_number'] = "Roll number already exists.";
        }

        //Student Class
        if (empty($request->student_class)) {
            $errors['student_class'] = "Student class is required.";
        }

        //Date of Birth
        if (empty($request->date_of_birth)) {
            $errors['date_of_birth'] = "Date of birth is required.";
        } elseif (!strtotime($request->date_of_birth)) {
            $errors['date_of_birth'] = "Invalid date format for date of birth.";
        }

        //Admission Date
        if (empty($request->admission_date)) {
            $errors['admission_date'] = "Admission date is required.";
        } elseif (!strtotime($request->admission_date)) {
            $errors['admission_date'] = "Invalid date format for admission date.";
        }

        //Assigned Teacher ID (Optional but must exist if provided)
        if (!empty($request->assigned_teacher_id) && !Teacher::where('id', $request->assigned_teacher_id)->exists()) {
        $errors['assigned_teacher_id'] = "Assigned teacher does not exist.";
    }

    if (!empty($errors)) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $errors
        ], 422);
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

    // Default for admin or other roles
    return response()->json($user);
}


    // Logout
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
}

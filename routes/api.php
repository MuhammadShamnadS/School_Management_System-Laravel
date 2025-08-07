<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;



//Authentication
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::post('/refresh-token', [AuthController::class, 'refreshWithToken']);





//Admin CRUD (Teachers + Students)
Route::middleware(['auth:api', 'role:admin'])->group(function () {

    Route::post('/register/teacher', [AuthController::class, 'registerTeacher']);
    Route::post('/register/student', [AuthController::class, 'registerStudent']);
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('students', StudentController::class);
    Route::get('/teachers/{teacherId}/students', [StudentController::class, 'studentsByTeacher']);
});

//Teacher Routes
Route::middleware(['auth:api', 'role:teacher'])->group(function () {
    Route::get('/my-detail', [TeacherController::class, 'myProfile']); 
    Route::get('/my-students', [StudentController::class, 'myStudents']); 
});

//Student Routes
Route::middleware(['auth:api', 'role:student'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/my-details', [StudentController::class, 'myProfile']);
});


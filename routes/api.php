<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;



//Authentication
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register'])->middleware(['auth:api', 'role:admin']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');


//Admin CRUD (Teachers + Students)
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('students', StudentController::class);
});

//Teacher Routes
Route::middleware(['auth:api', 'role:teacher'])->group(function () {
    Route::get('my-teacher', [TeacherController::class, 'myProfile']); 
    Route::get('my-students', [StudentController::class, 'myStudents']); 
});

//Student Routes
Route::middleware(['auth:api', 'role:student'])->get('my-student', [StudentController::class, 'myProfile']);
Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');


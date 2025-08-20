<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\MessageController;






//Authentication
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::post('/refresh-token', [AuthController::class, 'refreshWithToken']);





//Admin CRUD (Teachers + Students)
Route::middleware(['auth:api', 'checkUserExists' , 'role:admin'])->group(function () {

    Route::post('/register/teacher', [AuthController::class, 'registerTeacher']);
    Route::post('/register/student', [AuthController::class, 'registerStudent']);
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('students', StudentController::class);
    Route::get('/teachers/{teacherId}/students', [StudentController::class, 'studentsByTeacher']);
});

//Teacher Routes
Route::middleware(['auth:api', 'checkUserExists' , 'role:teacher'])->group(function () {
    Route::get('/my-detail', [TeacherController::class, 'myProfile']); 
    Route::get('/my-students', [StudentController::class, 'myStudents']); 
});

//Student Routes
Route::middleware(['auth:api', 'checkUserExists' , 'role:student'])->group(function () {
    Route::get('/my-details', [StudentController::class, 'myProfile']);
});



Route::middleware('auth:api' , 'checkUserExists')->group(function () {
    Route::get('/messages/{user}', [MessageController::class, 'index']); // history
    Route::post('/messages', [MessageController::class, 'send']);       // send


});








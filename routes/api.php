<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;


//Authentication
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register'])->middleware(['auth:api', 'role:admin']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');


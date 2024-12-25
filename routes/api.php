<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AppointmentController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

// create user or register user 
Route::post('/create/user',[UserController::class,'store']);

// User Login 
Route::post('/login', [UserController::class, 'login']);

// middleware Get user by its id 
Route::middleware('auth:api')->get('/user/{id}', [UserController::class, 'show']);

// Get all users
Route::middleware(['auth:api', 'admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});


//Appointment 
Route::middleware('auth:api')->post('/create/appointment',[AppointmentController::class,'store']);


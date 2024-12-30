<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Middleware\AdminMiddleware;

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
// Route::get('/users',[UserController::class,'index']);
// Route::get('/users', [UserController::class, 'index'])->middleware(AdminMiddleware::class);
Route::get('/users', [UserController::class, 'index'])
    ->middleware(['auth:api', \App\Http\Middleware\AdminMiddleware::class]);


Route::patch('/update/{appointment}', [AppointmentController::class, 'updateStatus'])
->middleware(['auth:api', \App\Http\Middleware\AdminMiddleware::class]);





//Appointment 
Route::middleware('auth:api')->post('/create/appointment',[AppointmentController::class,'store']);


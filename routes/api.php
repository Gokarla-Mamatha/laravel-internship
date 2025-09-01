<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

Route::post('registers', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);
Route::middleware('auth:api')->get('profile', [AuthController::class, 'profile']);

Route::post('register', [UserController::class, 'register']);

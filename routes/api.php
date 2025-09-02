<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OtpController;

Route::post('registers', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);
Route::middleware('auth:api')->get('profile', [AuthController::class, 'profile']);

Route::post('register', [UserController::class, 'register']);

// OTP Routes
Route::post('otp/send', [OtpController::class, 'sendOtp']);
Route::post('otp/verify', [OtpController::class, 'verifyOtp']);

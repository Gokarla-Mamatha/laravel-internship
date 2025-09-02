<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Otp;
use App\Models\User;
use App\Services\EmailService;
use App\Services\SmsService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OtpController extends Controller
{
    protected $emailService;
    protected $smsService;

    public function __construct(EmailService $emailService, SmsService $smsService)
    {
        $this->emailService = $emailService;
        $this->smsService = $smsService;
    }

    /**
     * Send OTP to user via SMS and email
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required_without:email|string',
            'email' => 'required_without:phone|email',
            'type' => 'in:login,reset,verification'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate 6-digit OTP
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Set expiry to 5 minutes from now
            $expiresAt = Carbon::now()->addMinutes(5);

            $this->invalidateExistingOtps($request->phone, $request->email, $request->type ?? 'verification');

            $otpRecord = Otp::create([
                'user_id' => $this->getUserId($request->phone, $request->email),
                'email' => $request->email,
                'phone' => $request->phone,
                'otp' => $otp,
                'expires_at' => $expiresAt,
                'type' => $request->type ?? 'verification'
            ]);

            $smsSent = $this->smsService->sendOtpSms($request->phone, $otp);
            
            $emailSent = false;
            if ($request->email) {
                $emailSent = $this->emailService->sendOtpEmail($request->email, $otp);
                
                if (!$emailSent) {
                    Log::warning("Failed to send OTP email to: {$request->email}, but SMS was sent successfully");
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'data' => [
                    'sms_sent' => $smsSent,
                    'email_sent' => $emailSent,
                    'expires_in' => 300, 
                    'contact' => $request->phone ?: $request->email
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error("OTP sending failed: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required_without:email|string',
            'email' => 'required_without:phone|email',
            'otp' => 'required|string|size:6',
            'type' => 'in:login,reset,verification'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find valid OTP
            $otpRecord = Otp::valid()
                ->when($request->phone, function($query) use ($request) {
                    return $query->byPhone($request->phone);
                })
                ->when($request->email, function($query) use ($request) {
                    return $query->byEmail($request->email);
                })
                ->where('type', $request->type ?? 'verification')
                ->latest()
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP'
                ], 400);
            }

            // Mark OTP as used
            $otpRecord->markAsUsed();

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully',
                'data' => [
                    'user_id' => $otpRecord->user_id,
                    'contact' => $request->phone ?: $request->email
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error("OTP verification failed: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OTP. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Invalidate existing OTPs for a user/contact
     */
    private function invalidateExistingOtps($phone, $email, $type)
    {
        Otp::where(function($query) use ($phone, $email) {
            if ($phone) {
                $query->where('phone', $phone);
            }
            if ($email) {
                $query->orWhere('email', $email);
            }
        })
        ->where('type', $type)
        ->where('is_used', false)
        ->update(['is_used' => true]);
    }

    /**
     * Get user ID by phone or email
     */
    private function getUserId($phone, $email)
    {
        if ($phone) {
            $user = User::where('phone', $phone)->first();
            return $user ? $user->id : null;
        }
        
        if ($email) {
            $user = User::where('email', $email)->first();
            return $user ? $user->id : null;
        }
        
        return null;
    }
}

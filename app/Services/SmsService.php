<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send OTP via SMS
     *
     * @param string $phoneNumber
     * @param string $otp
     * @return bool
     */
    public function sendOtpSms(string $phoneNumber, string $otp): bool
    {
        try {
            // TODO: Implement actual SMS gateway integration
            // For now, just log the OTP (in production, integrate with SMS provider)
            Log::info("SMS OTP sent to {$phoneNumber}: {$otp}");
            
            // Simulate SMS sending success
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send SMS OTP to {$phoneNumber}: " . $e->getMessage());
            return false;
        }
    }
}

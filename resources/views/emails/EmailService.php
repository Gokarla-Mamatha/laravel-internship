<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Message;

class EmailService
{
    /**
     * Send OTP email to user
     *
     * @param string $userEmail
     * @param string $otp
     * @return bool
     */
    public function sendOtpEmail(string $userEmail, string $otp): bool
    {
        try {
            Mail::send('Greencreon', ['otp' => $otp], function (Message $message) use ($userEmail, $otp) {
                $message->to($userEmail)
                        ->subject("Your Greencreon OTP Code – [{$otp}]")
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            Log::info("OTP email sent successfully to: {$userEmail}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send OTP email to {$userEmail}: " . $e->getMessage());
            return false;
        }
    }
}

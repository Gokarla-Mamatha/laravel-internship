<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP Code</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px;">
        <!-- Header -->
        <div style="text-align: center; padding: 20px 0; border-bottom: 2px solid #4CAF50;">
            <h1 style="color: #4CAF50; margin: 0; font-size: 28px;">Greencreon</h1>
            <p style="color: #666; margin: 5px 0 0 0; font-size: 16px;">Secure Authentication</p>
        </div>

        <!-- Main Content -->
        <div style="padding: 30px 20px; text-align: center;">
            <h2 style="color: #333; margin-bottom: 20px;">Your OTP Code</h2>
            
            <div style="background-color: #f8f9fa; border: 2px dashed #4CAF50; border-radius: 10px; padding: 25px; margin: 25px 0;">
                <h1 style="color: #4CAF50; font-size: 36px; margin: 0; letter-spacing: 5px; font-weight: bold;">{{ $otp }}</h1>
            </div>
            
            <p style="color: #666; font-size: 16px; margin: 20px 0;">
                Use this code to complete your authentication process.
            </p>
            
            <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 15px; margin: 20px 0;">
                <p style="color: #856404; margin: 0; font-size: 14px;">
                    <strong>⚠️ Important:</strong> This code is valid for <strong>5 minutes only</strong> and can be used once.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div style="text-align: center; padding: 20px 0; border-top: 1px solid #eee; color: #666; font-size: 14px;">
            <p style="margin: 5px 0;">
                If you didn't request this code, please ignore this email.
            </p>
            <p style="margin: 5px 0;">
                Need help? Contact our support team at 
                <a href="mailto:support@Greencreon.com" style="color: #4CAF50; text-decoration: none;">support@Greencreon.com</a>
            </p>
            <p style="margin: 10px 0 0 0; font-size: 12px;">
                © {{ date('Y') }} Greencreon. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>

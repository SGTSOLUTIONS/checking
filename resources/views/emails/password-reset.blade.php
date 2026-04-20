<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - SGT Solution</title>
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .btn-reset {
            display: inline-block;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin: 20px 0;
        }
        .code-box {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            padding: 15px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #495057;
            margin: 20px 0;
            border-radius: 5px;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Password Reset Request</h1>
            <p>SGT Solution Account</p>
        </div>
        
        <div class="email-body">
            <p>Hello,</p>
            <p>You are receiving this email because we received a password reset request for your account.</p>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="btn-reset">
                    Reset Your Password
                </a>
            </div>
            
            <p>This password reset link will expire in 60 minutes.</p>
            
            <p>If you did not request a password reset, no further action is required. Please ignore this email.</p>
            
            <p style="margin-top: 30px;">If you're having trouble clicking the button, copy and paste the URL below into your web browser:</p>
            <div class="code-box">
                {{ $resetUrl }}
            </div>
        </div>
        
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} SGT Solution. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: #0d6efd;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .button {
            display: inline-block;
            background: #0d6efd;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Corporation Portal</h2>
            <p>Password Reset Request</p>
        </div>
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            <p>We received a request to reset your password for your Corporation Portal account.</p>
            <p>Click the button below to reset your password:</p>
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button">Reset Password</a>
            </div>
            <p>If you did not request a password reset, please ignore this email. This link will expire in 60 minutes.</p>
            <p>For security reasons, do not share this link with anyone.</p>
            <br>
            <p>Thank you,<br>Corporation Portal Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Corporation Portal. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>

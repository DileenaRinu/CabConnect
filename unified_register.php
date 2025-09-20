<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CabConnect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .register-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ff6b6b, #feca57, #48dbfb, #ff9ff3, #54a0ff);
            background-size: 300% 300%;
            animation: gradientShift 3s ease infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #333;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .logo p {
            color: #666;
            font-size: 1rem;
        }

        .user-type-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }

        .user-type-btn {
            flex: 1;
            padding: 12px;
            border: 2px solid #e1e8ed;
            background: #f8f9fa;
            border-radius: 12px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .user-type-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .register-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link p {
            color: #666;
            margin-bottom: 10px;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 20px;
            border: 2px solid #667eea;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .back-home {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .back-home:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <a href="homePage.php" class="back-home">← Back to Home</a>
    
    <div class="register-container">
        <div class="logo">
            <h1>CabConnect</h1>
            <p>Join us today! Select your account type</p>
        </div>

        <div class="user-type-selector">
            <div class="user-type-btn active" onclick="selectUserType(event, 'customer')">
                <strong>Customer</strong><br>
                <small>Book rides easily</small>
            </div>
            <div class="user-type-btn" onclick="selectUserType(event, 'driver')">
                <strong>Driver</strong><br>
                <small>Earn by driving</small>
            </div>
        </div>

        <div class="register-btn" onclick="proceedToRegistration()">
            Continue Registration
        </div>

        <div class="login-link">
            <p>Already have an account?</p>
            <a href="unified_login.php">Sign In</a>
        </div>
    </div>

    <script>
        let selectedUserType = 'customer';

        function selectUserType(event, type) {
            selectedUserType = type;
            const buttons = document.querySelectorAll('.user-type-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            event.currentTarget.classList.add('active');
        }

        function proceedToRegistration() {
            if (selectedUserType === 'customer') {
                window.location.href = 'customer_register.php';
            } else if (selectedUserType === 'driver') {
                window.location.href = 'driver_register.php';
            }
        }
    </script>
</body>
</html>
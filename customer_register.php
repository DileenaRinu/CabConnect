<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } elseif (emailExists($email, $conn)) {
        $error = "Email already exists";
    } else {
        // Generate unique customer ID
        $customer_id = generateUniqueId('C', 'customer', 'customer_id', $conn);

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert customer
        $stmt = $conn->prepare("INSERT INTO customer (customer_id, name, email, phone, address, password, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssss", $customer_id, $name, $email, $phone, $address, $hashed_password);

        if ($stmt->execute()) {
            // Registration successful, redirect to login page
            header("Location: unified_login.php?registered=customer");
            exit;
        } else {
            $error = "❌ Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration - CabConnect</title>
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

    .header {
        width: 100%;
        background: transparent;
        color: #333;
        padding: 0;
        box-shadow: none;
        position: absolute;
        top: 0;
        left: 0;
    }
    .header-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0 2rem;
    }
    .logo h1 {
        font-size: 2.5rem;
        font-weight: 700;
        letter-spacing: 1px;
        color: #333;
        margin-top: 30px;
    }
    .main-container {
        width: 100%;
        max-width: 600px;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 60px;
    }
    .card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 40px;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
        margin: 0 auto;
    }
    .card::before {
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
    .card h2 {
        color: #333;
        margin-bottom: 1.5rem;
        font-size: 1.7rem;
        text-align: center;
        font-weight: 700;
    }
    .form-group {
        margin-bottom: 1.2rem;
        position: relative;
    }
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #333;
        font-weight: 500;
    }
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="password"] {
        width: 100%;
        padding: 15px 20px;
        border: 2px solid #e1e8ed;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #f8f9fa;
        font-family: inherit;
    }
    .form-group input:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    .btn {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 0.5rem;
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.18);
    }
    .error, .success, .ajax-error {
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 1rem;
        font-weight: 500;
        text-align: center;
    }
    .error, .ajax-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .login-link {
        text-align: center;
        margin-top: 1.5rem;
        color: #555;
    }
    .login-link a {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
    }
    .login-link a:hover {
        text-decoration: underline;
    }
    @media (max-width: 600px) {
        .main-container {
            padding: 0 0.5rem;
        }
        .card {
            padding: 1.5rem 0.7rem 1.2rem 0.7rem;
        }
    }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <h1>CabConnect</h1><br><br>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        <div class="card">
            <h2>Customer Registration</h2>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="ajax-error error" style="display:none;"></div>
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" required value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" required value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" required value="<?php echo isset($address) ? htmlspecialchars($address) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
            <div class="login-link">
                Already have an account? <a href="unified_login.php">Login here</a>
            </div>
        </div>
    </div>
    <script>
    function ajaxCheck(field, value) {
        var formData = new FormData();
        formData.append(field, value);
        fetch('ajax_check_customer.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            showAjaxError(data);
        });
    }

    document.querySelector('input[name="email"]').addEventListener('blur', function() {
        var email = this.value.trim();
        if (email.length > 0) ajaxCheck('email', email);
    });

    document.querySelector('input[name="phone"]').addEventListener('blur', function() {
        var phone = this.value.trim();
        if (phone.length > 0) ajaxCheck('phone', phone);
    });

    function showAjaxError(data) {
        let errorDiv = document.querySelector('.ajax-error');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'ajax-error error';
            document.querySelector('form').insertBefore(errorDiv, document.querySelector('form').firstChild);
        }
        if (data.exists) {
            errorDiv.textContent = data.message;
            errorDiv.style.display = 'block';
        } else {
            errorDiv.textContent = '';
            errorDiv.style.display = 'none';
        }
    }
    </script>
</body>
</html>
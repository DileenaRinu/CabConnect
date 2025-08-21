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
<html>
<head>
    <title>Customer Registration - CabConnect</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; }
        .register-container {
            max-width: 400px;
            margin: 60px auto;
            background: #fff;
            padding: 2rem 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        h2 { text-align: center; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 6px;
        }
        .btn {
            width: 100%; padding: 0.7rem; background: #667eea; color: #fff;
            border: none; border-radius: 6px; font-size: 1rem; cursor: pointer;
            margin-top: 1rem;
        }
        .btn:hover { background: #5a67d8; }
        .error { color: #c00; margin-bottom: 1rem; text-align: center; }
        .success { color: #090; margin-bottom: 1rem; text-align: center; }
        .login-link { text-align: center; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="register-container">
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

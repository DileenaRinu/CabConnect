<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } else {

        // 1. Check Customer
        $stmt = $conn->prepare("SELECT customer_id, name, password FROM customer WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION["user_id"] = $user['customer_id'];
                $_SESSION["user_name"] = $user['name'];
                $_SESSION["user_type"] = "customer";
                header("Location: customer_dashboard.php");
                exit;
            } else {
                $error = "Invalid password";
            }
        } else {
            // 2. Check Driver
            $stmt = $conn->prepare("SELECT driver_id, name, password, approval_status FROM driver WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    if ($user['approval_status'] === 'Approved') {
                        $_SESSION["user_id"] = $user['driver_id'];
                        $_SESSION["user_name"] = $user['name'];
                        $_SESSION["user_type"] = "driver";
                        header("Location: driver_dashboard.php");
                        exit;
                    } else {
                        $error = "Your driver account is pending or rejected.";
                    }
                } else {
                    $error = "Invalid password";
                }
            } else {
                // 3. Check Admin
                $stmt = $conn->prepare("SELECT admin_id, name, password FROM admin WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    if (password_verify($password, $user['password'])) {
                        $_SESSION["admin_id"] = $user['admin_id'];
                        $_SESSION["user_id"] = $user['admin_id'];
                        $_SESSION["user_name"] = $user['name'];
                        $_SESSION["user_type"] = "admin";
                        header("Location: admin_dashboard.php");
                        exit;
                    } else {
                        $error = "Invalid password";
                    }
                } else {
                    $error = "No account found with this email.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - CabConnect</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 15px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .btn {
            width: 100%;
            background: #667eea;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn:hover {
            background: #556cd6;
        }
        .error {
            color: #c00;
            background: #fee;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            border: 1px solid #fcc;
        }
        .back-home {
            text-align: center;
            margin-top: 15px;
        }
        .back-home a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>CabConnect Login</h2>

    <!-- ✅ Error Display Block -->
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <input type="email" name="email" placeholder="Email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="btn">Sign In</button>
    </form>

    <div class="back-home">
        <a href="homePage.php">← Back to Home</a>
    </div>
</div>



</body>
</html>

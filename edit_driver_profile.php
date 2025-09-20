<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'driver') {
    header("Location: unified_login.php");
    exit;
}

$driver_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM driver WHERE driver_id = ?");
$stmt->bind_param("s", $driver_id);
$stmt->execute();
$driver = $stmt->get_result()->fetch_assoc();

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $vehicle_type = trim($_POST["vehicle_type"]);
    $vehicle_number = trim($_POST["vehicle_number"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($name) || empty($email) || empty($phone) || empty($vehicle_type) || empty($vehicle_number)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (!empty($password) && $password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT driver_id FROM driver WHERE email = ? AND driver_id != ?");
        $stmt->bind_param("ss", $email, $driver_id);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();
        if ($existing) {
            $error = "Email already in use by another account.";
        } else {
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE driver SET name=?, email=?, phone=?, vehicle_type=?, vehicle_number=?, password=? WHERE driver_id=?");
                $stmt->bind_param("sssssss", $name, $email, $phone, $vehicle_type, $vehicle_number, $hashed_password, $driver_id);
            } else {
                $stmt = $conn->prepare("UPDATE driver SET name=?, email=?, phone=?, vehicle_type=?, vehicle_number=? WHERE driver_id=?");
                $stmt->bind_param("ssssss", $name, $email, $phone, $vehicle_type, $vehicle_number, $driver_id);
            }
            if ($stmt->execute()) {
                $success = "Profile updated successfully.";
                $stmt = $conn->prepare("SELECT * FROM driver WHERE driver_id = ?");
                $stmt->bind_param("s", $driver_id);
                $stmt->execute();
                $driver = $stmt->get_result()->fetch_assoc();
            } else {
                $error = "Update failed. Try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Driver Profile</title>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .edit-container {
            background: #fff;
            padding: 2.5rem 2rem;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(102,126,234,0.12);
            width: 100%;
            max-width: 420px;
        }
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }
        .form-group {
            margin-bottom: 1.2rem;
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
            padding: 12px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 15px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .btn {
            width: 100%;
            padding: 15px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 0.5rem;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.18);
        }
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-weight: 500;
            text-align: center;
        }
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <h2>Edit Profile</h2>
        <?php if ($error): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($driver['name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($driver['email']); ?>" required>
            </div>
            <div class="form-group">
                <label>Phone:</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($driver['phone']); ?>" required>
            </div>
            <div class="form-group">
                <label>Vehicle Type:</label>
                <select name="vehicle_type" required style="width:100%;padding:12px 15px;border:2px solid #e1e8ed;border-radius:10px;font-size:15px;background:#f8f9fa;">
                    <option value="Car" <?php if($driver['vehicle_type']=="Car") echo "selected"; ?>>Car</option>
                    <option value="AutoRickshaw" <?php if($driver['vehicle_type']=="AutoRickshaw") echo "selected"; ?>>AutoRickshaw</option>
                    <option value="TwoWheeler" <?php if($driver['vehicle_type']=="TwoWheeler") echo "selected"; ?>>TwoWheeler</option>
                </select>
            </div>
            <div class="form-group">
                <label>Vehicle Number:</label>
                <input type="text" name="vehicle_number" value="<?php echo htmlspecialchars($driver['vehicle_number']); ?>" required>
            </div>
            <div class="form-group">
                <label>New Password:</label>
                <input type="password" name="password" placeholder="Leave blank to keep current password">
            </div>
            <div class="form-group">
                <label>Confirm New Password:</label>
                <input type="password" name="confirm_password" placeholder="Leave blank to keep current password">
            </div>
            <button type="submit" class="btn">Save Changes</button>
        </form>
        <a href="driver_dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
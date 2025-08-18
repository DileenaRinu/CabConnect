<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $license_number = trim($_POST["license_number"]);
    $vehicle_type = $_POST["vehicle_type"];
    $vehicle_model = trim($_POST["vehicle_model"]);
    $vehicle_number = trim($_POST["vehicle_number"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    
    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($license_number) || empty($vehicle_model) || empty($vehicle_number) || empty($password)) {
        $error = "Please fill in all fields";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } elseif (emailExists($email, $conn)) {
        $error = "Email already exists";
    } else {
        // Generate unique driver ID
        $driver_id = generateUniqueId('D', 'driver', 'driver_id', $conn);
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // File upload handling
        $upload_dir = "uploads/drivers/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        function uploadFile($file, $allowed_types, $upload_dir) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = uniqid() . '_' . time() . '.' . $ext;
            $target = $upload_dir . $filename;
            if (in_array($ext, $allowed_types) && move_uploaded_file($file['tmp_name'], $target)) {
                return $target;
            }
            return false;
        }

        $license_image_path = uploadFile($_FILES['license_image'], ['jpg','jpeg','png','gif','bmp','webp'], $upload_dir);
        $passport_photo_path = uploadFile($_FILES['passport_photo'], ['jpg','jpeg','png','gif','bmp','webp'], $upload_dir);
        $id_proof_path = uploadFile($_FILES['id_proof'], ['jpg','jpeg','png','gif','bmp','webp','pdf'], $upload_dir);
        $aadhar_pdf_path = uploadFile($_FILES['aadhar_pdf'], ['pdf'], $upload_dir);

        if (!$license_image_path || !$passport_photo_path || !$id_proof_path || !$aadhar_pdf_path) {
            $error = "File upload failed. Please check your files and try again.";
        } else {
            // Insert driver
            $stmt = $conn->prepare("INSERT INTO driver (driver_id, name, email, phone, address, license_number, vehicle_type, vehicle_model, vehicle_number, password, approval_status, availability_status, created_at, license_file, photo, id_proof) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', 'Not Available', NOW(), ?, ?, ?)");
            $stmt->bind_param("ssssssssssss", $driver_id, $name, $email, $phone, $address, $license_number, $vehicle_type, $vehicle_model, $vehicle_number, $hashed_password, $license_image_path, $passport_photo_path, $id_proof_path);
            
            if ($stmt->execute()) {
                $success = "Registration successful! Your account is pending admin approval.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Registration - CabConnect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Toza, Geneva, Verdana, sans-serif;
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
            max-width: 600px;
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

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
            font-family: inherit;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            height: 80px;
            resize: vertical;
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
            margin-bottom: 20px;
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
        }

        .error {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #fcc;
        }

        .success {
            background: #efe;
            color: #3c3;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #cfc;
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
            background: rgba(255, 2255, 255, 0.3);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <a href="register.php" class="back-home">← Back</a>
    
    <div class="register-container">
        <div class="logo">
            <h1>CabConnect</h1>
            <p>Driver Registration</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required 
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="license_number">License Number</label>
                    <input type="text" id="license_number" name="license_number" required 
                           value="<?php echo isset($_POST['license_number']) ? htmlspecialchars($_POST['license_number']) : ''; ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="vehicle_type">Vehicle Type</label>
                    <select id="vehicle_type" name="vehicle_type" required>
                        <option value="">Select Vehicle Type</option>
                        <option value="Car" <?php echo (isset($_POST['vehicle_type']) && $_POST['vehicle_type'] == 'Car') ? 'selected' : ''; ?>>Car</option>
                        <option value="Two-wheeler" <?php echo (isset($_POST['vehicle_type']) && $_POST['vehicle_type'] == 'Two-wheeler') ? 'selected' : ''; ?>>Two-wheeler</option>
                        <option value="Auto-rickshaw" <?php echo (isset($_POST['vehicle_type']) && $_POST['vehicle_type'] == 'Auto-rickshaw') ? 'selected' : ''; ?>>Auto-rickshaw</option>
                        <option value="Heavy Vehicle" <?php echo (isset($_POST['vehicle_type']) && $_POST['vehicle_type'] == 'Heavy Vehicle') ? 'selected' : ''; ?>>Heavy Vehicle</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="vehicle_model">Vehicle Model</label>
                    <input type="text" id="vehicle_model" name="vehicle_model" required 
                           value="<?php echo isset($_POST['vehicle_model']) ? htmlspecialchars($_POST['vehicle_model']) : ''; ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="vehicle_number">Vehicle Number</label>
                <input type="text" id="vehicle_number" name="vehicle_number" required 
                       value="<?php echo isset($_POST['vehicle_number']) ? htmlspecialchars($_POST['vehicle_number']) : ''; ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
            </div>

            <div class="form-group">
                <label for="license_image">Upload License Image</label>
                <input type="file" id="license_image" name="license_image" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="passport_photo">Upload Passport Size Photo</label>
                <input type="file" id="passport_photo" name="passport_photo" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="id_proof">Upload ID Proof (Image or PDF)</label>
                <input type="file" id="id_proof" name="id_proof" accept="image/*,application/pdf" required>
            </div>
            <div class="form-group">
                <label for="aadhar_pdf">Upload Aadhaar Card (PDF)</label>
                <input type="file" id="aadhar_pdf" name="aadhar_pdf" accept="application/pdf" required>
            </div>

            <button type="submit" class="register-btn">Register as Driver</button>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="login.php">Sign In</a></p>
        </div>
    </div>
</body>
</html>
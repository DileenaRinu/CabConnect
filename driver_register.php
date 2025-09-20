<?php
session_start();
include 'db_connect.php';

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
    return null;
}

$license_file_path = isset($_FILES['license_image']) ? uploadFile($_FILES['license_image'], ['jpg','jpeg','png','gif','bmp','webp'], $upload_dir) : null;
$photo_path = isset($_FILES['passport_photo']) ? uploadFile($_FILES['passport_photo'], ['jpg','jpeg','png','gif','bmp','webp'], $upload_dir) : null;
$vehicle_photo_path = isset($_FILES['vehicle_image']) ? uploadFile($_FILES['vehicle_image'], ['jpg','jpeg','png','gif','bmp','webp'], $upload_dir) : null; // <-- changed variable name
$id_proof_path = isset($_FILES['id_proof']) ? uploadFile($_FILES['id_proof'], ['pdf'], $upload_dir) : null;
$aadhar_pdf_path = isset($_FILES['aadhar_pdf']) ? uploadFile($_FILES['aadhar_pdf'], ['pdf'], $upload_dir) : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $license_number = trim($_POST["license_number"]);
    $vehicle_type = $_POST["vehicle_type"];
    $vehicle_number = trim($_POST["vehicle_number"]);
    $vehicle_model = trim($_POST["vehicle_model"]);
    
    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($password) || empty($license_number) || empty($vehicle_type) || empty($vehicle_number) || empty($vehicle_model)) {
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

        // Insert driver with pending approval status
        $stmt = $conn->prepare("INSERT INTO driver (
            driver_id, name, email, phone, address, license_number, vehicle_type, vehicle_model, vehicle_number,
            vehicle_photo, photo, license_file, id_proof, password, approval_status, availability_status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', 'Not Available', NOW())");
        $stmt->bind_param(
            "ssssssssssssss",
            $driver_id, $name, $email, $phone, $address, $license_number, $vehicle_type, $vehicle_model, $vehicle_number,
            $vehicle_photo_path, $photo_path, $license_file_path, $id_proof_path, $hashed_password
        );

        if ($stmt->execute()) {
            $success = "Registration successful! Your account is pending admin approval. You will be notified once approved.";
        } else {
            $error = "Registration failed. Please try again.";
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
            max-width: 600px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            max-height: 90vh;
            overflow-y: auto;
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
            margin-bottom: 20px;
        }

        .form-group {
            flex: 1;
            position: relative;
        }

        .form-group.full-width {
            flex: none;
            width: 100%;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
            font-family: inherit;
        }

        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            height: 80px;
            resize: vertical;
        }

        .form-group select {
            cursor: pointer;
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
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .vehicle-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 2px solid #e1e8ed;
        }

        .vehicle-info h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .info-note {
            background: #e3f2fd;
            color: #1976d2;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #bbdefb;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .register-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <a href="unified_register.php" class="back-home">← Back</a>

    <div class="register-container">
        <div class="logo">
            <h1>CabConnect</h1>
            <p>Driver Registration</p>
        </div>

        <div class="info-note">
            <strong>Note:</strong> Your registration will be reviewed by our admin team. You'll receive approval notification before you can start accepting rides.
        </div>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="ajax-error error" style="display:none;"></div>
            <!-- Personal Information -->
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="johndoe@gmail.com" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone"  "required 
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="license_number">Driving License Number</label>
                    <input type="text" id="license_number" name="license_number" placeholder=" SS-RR-YYYYNNNNNNN" required 
                           value="<?php echo isset($_POST['license_number']) ? htmlspecialchars($_POST['license_number']) : ''; ?>">
                </div>
            </div>

            <div class="form-group full-width">
                <label for="address">Address</label>
                <textarea id="address" name="address" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
            </div>

            <!-- Vehicle Information -->
            <div class="vehicle-info">
                <h3>Vehicle Information</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="vehicle_type">Vehicle Type</label>
                        <select id="vehicle_type" name="vehicle_type" required>
                            <option value="">Select Vehicle Type</option>
                            <option value="Car" <?php echo (isset($_POST['vehicle_type']) && $_POST['vehicle_type'] == 'Car') ? 'selected' : ''; ?>>Car</option>
                            <option value="TwoWheeler" <?php echo (isset($_POST['vehicle_type']) && $_POST['vehicle_type'] == 'TwoWheeler') ? 'selected' : ''; ?>>Two-Wheeler</option>
                            <option value="AutoRickshaw" <?php echo (isset($_POST['vehicle_type']) && $_POST['vehicle_type'] == 'AutoRickshaw') ? 'selected' : ''; ?>>Auto-Rickshaw</option>

                    </div>
                    <div class="form-group">
                        <label for="vehicle_number">Vehicle Number</label>
                        <input type="text" id="vehicle_number" name="vehicle_number" required 
                               placeholder="e.g., KL-01-AB-1234"
                               value="<?php echo isset($_POST['vehicle_number']) ? htmlspecialchars($_POST['vehicle_number']) : ''; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="vehicle_model">Vehicle Model</label>
                    <input type="text" id="vehicle_model" name="vehicle_model" required 
                           placeholder="e.g., Honda City, Bajaj Pulsar, Mahindra Bolero"
                           value="<?php echo isset($_POST['vehicle_model']) ? htmlspecialchars($_POST['vehicle_model']) : ''; ?>">
                </div>
            </div>

            <!-- File Upload Section -->
            <div class="form-row">
                <div class="form-group">
                    <label for="license_image">Upload License Image</label>
                    <input type="file" id="license_image" name="license_image" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="passport_photo">Upload Passport Size Photo</label>
                    <input type="file" id="passport_photo" name="passport_photo" accept="image/*" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="vehicle_image">Upload Vehicle Photo</label>
                    <input type="file" id="vehicle_image" name="vehicle_image" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="id_proof">Upload ID Proof (PDF)</label>
                    <input type="file" id="id_proof" name="id_proof" accept="application/pdf" required>
                </div>
            </div>

            <!-- Password Section -->
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

            <button type="submit" class="register-btn">Register as Driver</button>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="unified_login.php">Sign In</a></p>
        </div>
    </div>

    <script>
function ajaxCheck(field, value) {
    var formData = new FormData();
    formData.append(field, value);
    fetch('ajax_check_driver.php', {
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

document.querySelector('input[name="vehicle_number"]').addEventListener('blur', function() {
    var vehicle_number = this.value.trim();
    if (vehicle_number.length > 0) ajaxCheck('vehicle_number', vehicle_number);
});

document.querySelector('input[name="license_number"]').addEventListener('blur', function() {
    var license_number = this.value.trim();
    if (license_number.length > 0) ajaxCheck('license_number', license_number);
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
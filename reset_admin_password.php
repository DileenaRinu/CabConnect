<?php
include 'db_connect.php';

// New password (change if needed)
$new_password = 'password123';
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

// Update admin password
$email = 'admin@cabconnect.com';
$stmt = $conn->prepare("UPDATE admin SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $hashed_password, $email);

if ($stmt->execute()) {
    echo "Admin password reset successfully.<br>";
    echo "New password: <b>password123</b>";
} else {
    echo "Failed to reset password.";
}
?>
<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $driver_id = $_SESSION['user_id'];

    // Check if driver is approved
    $stmt = $conn->prepare("SELECT approval_status FROM driver WHERE driver_id = ?");
    $stmt->bind_param("s", $driver_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['approval_status'] !== 'Approved') {
        header("Location: driver_dashboard.php?error=notapproved");
        exit;
    }

    // Only accept if not already accepted
    $stmt = $conn->prepare("UPDATE booking SET driver_id = ?, trip_status = 'Accepted' WHERE booking_id = ? AND driver_id IS NULL");
    $stmt->bind_param("ss", $driver_id, $booking_id);
    $stmt->execute();

    header("Location: driver_dashboard.php");
    exit;
}
?>
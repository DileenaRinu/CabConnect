<?php
include 'db_connect.php';

$booking_id = $_POST['booking_id'] ?? '';
$payment_id = $_POST['payment_id'] ?? '';

if ($booking_id && $payment_id) {
    $stmt = $conn->prepare("UPDATE booking SET payment_status = 'Completed' WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    echo "success";
} else {
    echo "error";
}
?>

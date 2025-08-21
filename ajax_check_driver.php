<?php
include 'db_connect.php';
header('Content-Type: application/json');

$response = ['exists' => false, 'field' => '', 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['email'])) {
        $email = trim($_POST['email']);
        $stmt = $conn->prepare("SELECT driver_id FROM driver WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $response['exists'] = true;
            $response['field'] = 'email';
            $response['message'] = 'Email already exists';
        }
    }
    if (!empty($_POST['phone'])) {
        $phone = trim($_POST['phone']);
        $stmt = $conn->prepare("SELECT driver_id FROM driver WHERE phone = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $response['exists'] = true;
            $response['field'] = 'phone';
            $response['message'] = 'Phone number already exists';
        }
    }
    if (!empty($_POST['vehicle_number'])) {
        $vehicle_number = trim($_POST['vehicle_number']);
        $stmt = $conn->prepare("SELECT driver_id FROM driver WHERE vehicle_number = ?");
        $stmt->bind_param("s", $vehicle_number);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $response['exists'] = true;
            $response['field'] = 'vehicle_number';
            $response['message'] = 'Vehicle number already exists';
        }
    }
    if (!empty($_POST['license_number'])) {
        $license_number = trim($_POST['license_number']);
        $stmt = $conn->prepare("SELECT driver_id FROM driver WHERE license_number = ?");
        $stmt->bind_param("s", $license_number);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $response['exists'] = true;
            $response['field'] = 'license_number';
            $response['message'] = 'License number already exists';
        }
    }
}
echo json_encode($response);
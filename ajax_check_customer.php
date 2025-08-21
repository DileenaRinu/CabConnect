<?php
include 'db_connect.php';
header('Content-Type: application/json');

$response = ['exists' => false, 'field' => '', 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['email'])) {
        $email = trim($_POST['email']);
        $stmt = $conn->prepare("SELECT customer_id FROM customer WHERE email = ?");
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
        $stmt = $conn->prepare("SELECT customer_id FROM customer WHERE phone = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $response['exists'] = true;
            $response['field'] = 'phone';
            $response['message'] = 'Phone number already exists';
        }
    }
}
echo json_encode($response);
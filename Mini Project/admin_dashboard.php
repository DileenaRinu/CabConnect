<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: unified_login.php");
    exit;
}

// Approve or Reject Driver
if (isset($_GET['action']) && isset($_GET['driver_id'])) {
    $driver_id = $_GET['driver_id'];
    $action = $_GET['action'];

    if ($action == 'approve') {
        $status = 'Approved';
    } elseif ($action == 'reject') {
        $status = 'Rejected';
    }

    $stmt = $conn->prepare("UPDATE driver SET approval_status = ? WHERE driver_id = ?");
    $stmt->bind_param("ss", $status, $driver_id);
    $stmt->execute();
}

// Delete Customer
if (isset($_GET['delete_customer'])) {
    $customer_id = $_GET['delete_customer'];
    $stmt = $conn->prepare("DELETE FROM customer WHERE customer_id = ?");
    $stmt->bind_param("s", $customer_id);
    $stmt->execute();
}

// Delete Driver
if (isset($_GET['delete_driver'])) {
    $driver_id = $_GET['delete_driver'];
    $stmt = $conn->prepare("DELETE FROM driver WHERE driver_id = ?");
    $stmt->bind_param("s", $driver_id);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - CabConnect</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background-color: #f2f2f2;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: white;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 10px;
            text-align: center;
        }
        .action-link {
            padding: 6px 10px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
        }
        .approve {
            background-color: green;
        }
        .reject {
            background-color: red;
        }
        .edit {
            background-color: #007bff;
        }
        .delete {
            background-color: #c00;
        }
        .logout-btn {
            float: right;
            padding: 6px 12px;
            background-color: #c00;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .section {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>

<a href="logout_php.php" class="logout-btn">Logout</a>
<h2>Welcome Admin!</h2>

<div class="section">
    <h3>Pending Driver Approvals</h3>
    <table>
        <tr>
            <th>Driver ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Vehicle</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php
        $stmt = $conn->prepare("SELECT * FROM driver WHERE approval_status = 'Pending'");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $vehicle_type = isset($row['vehicle_type']) ? $row['vehicle_type'] : '';
            $vehicle_name = isset($row['vehicle_name']) ? $row['vehicle_name'] : '';
            echo "<tr>
                    <td>{$row['driver_id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$vehicle_type}" . ($vehicle_name ? " - $vehicle_name" : "") . "</td>
                    <td>{$row['approval_status']}</td>
                    <td>
                        <a class='action-link approve' href='admin_dashboard.php?action=approve&driver_id={$row['driver_id']}'>Approve</a>
                        <a class='action-link reject' href='admin_dashboard.php?action=reject&driver_id={$row['driver_id']}'>Reject</a>
                    </td>
                </tr>";
        }
        ?>
    </table>
</div>

<div class="section">
    <h3>Registered Customers</h3>
    <table>
        <tr>
            <th>Customer ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
        <?php
        $stmt = $conn->prepare("SELECT * FROM customer");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['customer_id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['phone']}</td>
                    <td>
                        <a class='action-link edit' href='edit_customer.php?customer_id={$row['customer_id']}'>Edit</a>
                        <a class='action-link delete' href='admin_dashboard.php?delete_customer={$row['customer_id']}' onclick=\"return confirm('Are you sure you want to delete this customer?');\">Delete</a>
                    </td>
                </tr>";
        }
        ?>
    </table>
</div>

<div class="section">
    <h3>Registered Drivers</h3>
    <table>
        <tr>
            <th>Driver ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Vehicle</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php
        $stmt = $conn->prepare("SELECT * FROM driver");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $vehicle_type = isset($row['vehicle_type']) ? $row['vehicle_type'] : '';
            $vehicle_name = isset($row['vehicle_name']) ? $row['vehicle_name'] : '';
            echo "<tr>
                    <td>{$row['driver_id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['phone']}</td>
                    <td>{$vehicle_type}" . ($vehicle_name ? " - $vehicle_name" : "") . "</td>
                    <td>{$row['approval_status']}</td>
                    <td>
                        <a class='action-link edit' href='edit_driver.php?driver_id={$row['driver_id']}'>Edit</a>
                        <a class='action-link delete' href='admin_dashboard.php?delete_driver={$row['driver_id']}' onclick=\"return confirm('Are you sure you want to delete this driver?');\">Delete</a>
                    </td>
                </tr>";
        }
        ?>
    </table>
</div>

<div class="section">
    <h3>All Bookings</h3>
    <table>
        <tr>
            <th>Booking ID</th>
            <th>Customer ID</th>
            <th>Driver ID</th>
            <th>Pickup</th>
            <th>Drop</th>
            <th>Vehicle</th>
            <th>Status</th>
            <th>Fare</th>
        </tr>
        <?php
        $stmt = $conn->prepare("SELECT * FROM booking ORDER BY booking_time DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['booking_id']}</td>
                    <td>{$row['customer_id']}</td>
                    <td>" . ($row['driver_id'] ?? 'Not Assigned') . "</td>
                    <td>{$row['pickup_location']}</td>
                    <td>{$row['drop_location']}</td>
                    <td>{$row['vehicle_type']}</td>
                    <td>{$row['trip_status']}</td>
                    <td>{$row['fare']}</td>
                </tr>";
        }
        ?>
    </table>
</div>

</body>
</html>

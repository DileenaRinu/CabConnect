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
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 40px;
            background: linear-gradient(135deg, #667eea 0%, #48dbfb 100%);
            min-height: 100vh;
            margin: 0;
        }
        h2 {
            color: #fff;
            margin-bottom: 30px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .section {
            margin-bottom: 40px;
            background: rgba(255,255,255,0.95);
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            padding: 32px 24px 24px 24px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }
        .section h3 {
            color: #333;
            margin-bottom: 18px;
            font-size: 1.3rem;
            letter-spacing: 1px;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        th, td {
            padding: 14px 10px;
            text-align: center;
        }
        th {
            background: #2d4ea8ff;
            color: #fff;
            font-weight: 600;
            border-bottom: 2px solid #e0e0e0;
        }
        tr:nth-child(even) td {
            background: #f7fafd;
        }
        tr:hover td {
            background: #e3f0ff;
            transition: background 0.2s;
        }
        .action-link {
            padding: 7px 14px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            margin: 0 2px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            transition: background 0.2s, transform 0.2s;
        }
        .approve {
            background: #43e97b;
        }
        .reject {
            background: #fa709a;
            color: #fff;
        }
        .edit {
            background: #54a0ff;
        }
        .delete {
            background: #ff5858;
        }
        .logout-btn {
            float: right;
            padding: 8px 18px;
            background: #ff5858;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-bottom: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: background 0.2s;
        }
        .logout-btn:hover {
            background: #f09819;
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
            <th>Phone</th>
            <th>Address</th>
            <th>License No.</th>
            <th>Vehicle Type</th>
            <th>Vehicle Model</th>
            <th>Vehicle Number</th>
            <th>Vehicle Photo</th>
            <th>Photo</th>
            <th>License File</th>
            <th>ID Proof</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php
        $stmt = $conn->prepare("SELECT * FROM driver WHERE approval_status = 'Pending'");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['driver_id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['phone']}</td>
                    <td>{$row['address']}</td>
                    <td>{$row['license_number']}</td>
                    <td>{$row['vehicle_type']}</td>
                    <td>{$row['vehicle_model']}</td>
                    <td>{$row['vehicle_number']}</td>
                    <td>";
                        if (!empty($row['vehicle_photo'])) {
                            echo "<img src='{$row['vehicle_photo']}' alt='Vehicle' style='width:60px;height:40px;object-fit:cover;border-radius:6px;box-shadow:0 1px 4px rgba(0,0,0,0.08);'>";
                        } else {
                            echo "No Image";
                        }
                    echo "</td>
                    <td>";
                        if (!empty($row['photo'])) {
                            echo "<img src='{$row['photo']}' alt='Photo' style='width:40px;height:50px;object-fit:cover;border-radius:6px;box-shadow:0 1px 4px rgba(0,0,0,0.08);'>";
                        } else {
                            echo "No Photo";
                        }
                    echo "</td>
                    <td>";
                        if (!empty($row['license_file'])) {
                            echo "<a href='{$row['license_file']}' target='_blank' class='action-link edit'>View</a>";
                        } else {
                            echo "No File";
                        }
                    echo "</td>
                    <td>";
                        if (!empty($row['id_proof'])) {
                            echo "<a href='{$row['id_proof']}' target='_blank' class='action-link edit'>View</a>";
                        } else {
                            echo "No File";
                        }
                    echo "</td>
                    <td>{$row['approval_status']}</td>
                    <td style='display:flex; flex-direction:column; gap:8px; align-items:center;'>
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
            <th>Address</th>
            <th>License No.</th>
            <th>Vehicle Type</th>
            <th>Vehicle Model</th>
            <th>Vehicle Number</th>
            <th>Vehicle Photo</th>
            <th>Photo</th>
            <th>License File</th>
            <th>ID Proof</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php
        $stmt = $conn->prepare("SELECT * FROM driver");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['driver_id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['phone']}</td>
                    <td>{$row['address']}</td>
                    <td>{$row['license_number']}</td>
                    <td>{$row['vehicle_type']}</td>
                    <td>{$row['vehicle_model']}</td>
                    <td>{$row['vehicle_number']}</td>
                    <td>";
                        if (!empty($row['vehicle_photo'])) {
                            echo "<img src='{$row['vehicle_photo']}' alt='Vehicle' style='width:60px;height:40px;object-fit:cover;border-radius:6px;box-shadow:0 1px 4px rgba(0,0,0,0.08);'>";
                        } else {
                            echo "No Image";
                        }
                    echo "</td>
                    <td>";
                        if (!empty($row['photo'])) {
                            echo "<img src='{$row['photo']}' alt='Photo' style='width:40px;height:50px;object-fit:cover;border-radius:6px;box-shadow:0 1px 4px rgba(0,0,0,0.08);'>";
                        } else {
                            echo "No Photo";
                        }
                    echo "</td>
                    <td>";
                        if (!empty($row['license_file'])) {
                            echo "<a href='{$row['license_file']}' target='_blank' class='action-link edit'>View</a>";
                        } else {
                            echo "No File";
                        }
                    echo "</td>
                    <td>";
                        if (!empty($row['id_proof'])) {
                            echo "<a href='{$row['id_proof']}' target='_blank' class='action-link edit'>View</a>";
                        } else {
                            echo "No File";
                        }
                    echo "</td>
                    <td>{$row['approval_status']}</td>
                    <td style='display:flex; flex-direction:column; gap:8px; align-items:center;'>
                        <a class='action-link edit' href='edit_driver.php?driver_id={$row['driver_id']}'>Edit</a>
                        <a class='action-link delete' href='admin_dashboard.php?delete_driver={$row['driver_id']}' onclick=\"return confirm('Are you sure you want to delete this driver?');\">Delete</a>
                    </td>
                </tr>";
        }
        ?>
    </table>
</div>

<div class="section">
    <h3>All Trip Details</h3>
    <table>
        <tr>
            <th>#</th>
            <th>Customer Name</th>
            <th>Driver Name</th>
            <th>Vehicle Number</th>
            <th>Rate per Km</th>
            <th>Total Rate</th>
            <th>Trip Status</th>
            <th>Payment Status</th> <!-- Added column -->
        </tr>
        <?php
        $stmt = $conn->prepare("
            SELECT 
                b.*, 
                c.name AS customer_name, 
                d.name AS driver_name, 
                d.vehicle_number
            FROM booking b
            LEFT JOIN customer c ON b.customer_id = c.customer_id
            LEFT JOIN driver d ON b.driver_id = d.driver_id
            ORDER BY b.booking_time DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $i = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$i}</td>
                <td>".htmlspecialchars($row['customer_name'])."</td>
                <td>".htmlspecialchars($row['driver_name'])."</td>
                <td>".htmlspecialchars($row['vehicle_number'])."</td>
                <td>".(isset($row['rate_per_km']) ? '₹'.number_format($row['rate_per_km'],2) : '')."</td>
                <td>".(isset($row['fare']) ? '₹'.number_format($row['fare'],2) : '')."</td>
                <td>".htmlspecialchars($row['trip_status'])."</td>
                <td>".htmlspecialchars($row['payment_status'])."</td> <!-- Show payment status -->
            </tr>";
            $i++;
        }
        ?>
    </table>
</div>

</body>
</html>
<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'driver') {
    header("Location: unified_login.php");
    exit;
}

$driver_id = $_SESSION['user_id'];

// Fetch driver info
$stmt = $conn->prepare("SELECT * FROM driver WHERE driver_id = ?");
$stmt->bind_param("s", $driver_id);
$stmt->execute();
$driver = $stmt->get_result()->fetch_assoc();

if ($driver['approval_status'] !== 'Approved') {
    echo "<div class='card' style='margin:2rem auto;max-width:500px;text-align:center;'>
            <h2>Account Pending Approval</h2>
            <p>Your account is not yet approved by the admin. You will receive trip notifications once approved.</p>
          </div>";
    exit;
}

$vehicle_type = $driver['vehicle_type'];

// Fetch bookings for this driver
$stmt = $conn->prepare("SELECT b.*, c.name as customer_name, c.phone as customer_phone FROM booking b LEFT JOIN customer c ON b.customer_id = c.customer_id WHERE b.driver_id = ? ORDER BY b.booking_time DESC LIMIT 10");
$stmt->bind_param("s", $driver_id);
$stmt->execute();
$bookings = $stmt->get_result();

// Stats
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM booking WHERE driver_id = ?");
$stmt->bind_param("s", $driver_id);
$stmt->execute();
$total_bookings = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as completed FROM booking WHERE driver_id = ? AND trip_status = 'Completed'");
$stmt->bind_param("s", $driver_id);
$stmt->execute();
$completed_bookings = $stmt->get_result()->fetch_assoc()['completed'];

$stmt = $conn->prepare("SELECT COUNT(*) as pending FROM booking WHERE driver_id = ? AND trip_status = 'Pending'");
$stmt->bind_param("s", $driver_id);
$stmt->execute();
$pending_bookings = $stmt->get_result()->fetch_assoc()['pending'];

// Booking notifications for this vehicle type, not yet accepted
$stmt = $conn->prepare("SELECT * FROM booking WHERE vehicle_type = ? AND trip_status = 'Pending' AND driver_id IS NULL");
$stmt->bind_param("s", $vehicle_type);
$stmt->execute();
$pending_bookings_notifications = $stmt->get_result();

// Accept Trip
if (isset($_POST['accept_trip'])) {
    $booking_id = (int)$_POST['booking_id'];
    $distance = (float)$_POST['distance'];
    $rate_per_km = (float)$_POST['rate_per_km'];
    $total_rate = $distance * $rate_per_km;

    $stmt = $conn->prepare("UPDATE booking SET trip_status='Accepted', driver_id=?, distance=?, rate_per_km=?, fare=? WHERE booking_id=?");
    $stmt->bind_param("sdddi", $driver_id, $distance, $rate_per_km, $total_rate, $booking_id);
    $stmt->execute();

    // Update driver availability
    $stmt = $conn->prepare("UPDATE driver SET availability_status = 'Not Available' WHERE driver_id = ?");
    $stmt->bind_param("s", $driver_id);
    $stmt->execute();

    header("Location: driver_dashboard.php");
    exit;
}

// Start Trip
if (isset($_POST['start_trip'])) {
    $booking_id = (int)$_POST['booking_id'];
    $stmt = $conn->prepare("UPDATE booking SET trip_status = 'Started', start_time = NOW() WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    header("Location: driver_dashboard.php");
    exit;
}

// Complete Trip
if (isset($_POST['complete_trip'])) {
    $booking_id = (int)$_POST['booking_id'];
    $stmt = $conn->prepare("UPDATE booking SET trip_status = 'Completed', end_time = NOW() WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    
    // Update driver availability
    $stmt = $conn->prepare("UPDATE driver SET availability_status = 'Available' WHERE driver_id = ?");
    $stmt->bind_param("s", $driver_id);
    $stmt->execute();

    header("Location: driver_dashboard.php");
    exit;
}

// Refetch bookings for this driver after any status change
$stmt = $conn->prepare("SELECT b.*, c.name as customer_name, c.phone as customer_phone FROM booking b LEFT JOIN customer c ON b.customer_id = c.customer_id WHERE b.driver_id = ? ORDER BY b.booking_time DESC LIMIT 10");
$stmt->bind_param("s", $driver_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard - CabConnect</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #f8f9fa; color: #333; }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }
        .logo h1 { font-size: 2rem; font-weight: 700; }
        .user-info { display: flex; align-items: center; gap: 1rem; }
        .user-avatar {
            width: 40px; height: 40px; background: rgba(255,255,255,0.2);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; font-weight: bold;
        }
        .logout-btn {
            background: rgba(255,255,255,0.2); color: white; border: none;
            padding: 0.5rem 1rem; border-radius: 20px; cursor: pointer; text-decoration: none;
            transition: all 0.3s ease;
        }
        .logout-btn:hover { background: rgba(255,255,255,0.3); transform: translateY(-2px); }
        .main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        .card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, #ff6b6b, #feca57, #48dbfb, #ff9ff3, #54a0ff);
            background-size: 300% 300%;
            animation: gradientShift 3s ease infinite;
        }
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .card h2 { color: #333; margin-bottom: 1.5rem; font-size: 1.5rem; }
        .profile-info { display: grid; gap: 1rem; }
        .profile-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 0.5rem 0; border-bottom: 1px solid #eee;
        }
        .profile-item:last-child { border-bottom: none; }
        .profile-label { font-weight: 500; color: #666; }
        .profile-value { color: #333; font-weight: 600; }
        .stats-grid {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { font-size: 2rem; margin-bottom: 0.5rem; }
        .stat-number { font-size: 2rem; font-weight: 700; color: #667eea; margin-bottom: 0.25rem; }
        .stat-label { color: #666; font-size: 0.9rem; }
        .booking-history { grid-column: 1 / -1; }
        .booking-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
        }
        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .booking-id { font-weight: 600; color: #333; }
        .status {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status.pending { background: #fff3cd; color: #856404; }
        .status.accepted { background: #d1ecf1; color: #0c5460; }
        .status.completed { background: #d4edda; color: #155724; }
        .status.cancelled { background: #f8d7da; color: #721c24; }
        .booking-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 0.5rem;
        }
        .detail-item { font-size: 0.9rem; color: #666; }
        .detail-item strong { color: #333; }
        .no-bookings {
            text-align: center;
            color: #666;
            padding: 2rem;
            font-style: italic;
        }
        @media (max-width: 768px) {
            .main-container { grid-template-columns: 1fr; padding: 0 1rem; }
            .booking-details { grid-template-columns: 1fr; }
            .header-container { padding: 0 1rem; }
            .stats-grid { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); }
        }
        .accept-btn[disabled] {
            background: #ccc !important;
            color: #888 !important;
            cursor: not-allowed;
            opacity: 0.7;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f2f2f2;
            font-weight: 600;
        }
        tr:hover {
            background: #f9f9f9;
        }
        .section {
    overflow-x: auto;
}
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <h1>CabConnect</h1>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($driver['name'], 0, 1)); ?>
                </div>
                <span>Welcome, <?php echo htmlspecialchars($driver['name']); ?>!</span>
                <a href="logout_php.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-number"><?php echo $total_bookings; ?></div>
                <div class="stat-label">Total Rides</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-number"><?php echo $completed_bookings; ?></div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-number"><?php echo $pending_bookings; ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="card">
            <h2>👤 Your Profile</h2>
            <div class="profile-info" style="align-items: center;">
                <div style="text-align:center; margin-bottom:1rem;">
                    <?php if (!empty($driver['photo'])): ?>
                        <img src="<?php echo htmlspecialchars($driver['photo']); ?>" alt="Passport Photo" style="width:110px; height:140px; object-fit:cover; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.08); border:2px solid #eee;">
                        <div style="font-weight:600; margin-top:0.5rem;"><?php echo htmlspecialchars($driver['name']); ?></div>
                    <?php else: ?>
                        <div style="width:110px; height:140px; background:#eee; display:flex; align-items:center; justify-content:center; border-radius:10px; color:#aaa;">No Photo</div>
                    <?php endif; ?>

                    <?php if (!empty($driver['vehicle_image'])): ?>
                        <div style="margin-top:1rem;">
                            <img src="<?php echo htmlspecialchars($driver['vehicle_image']); ?>" alt="Vehicle Image" style="width:140px; height:90px; object-fit:cover; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.08); border:2px solid #eee;">
                            <div style="font-size:0.95rem; color:#555; margin-top:0.3rem;">Vehicle Image</div>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Existing profile details below -->
                <div class="profile-item">
                    <span class="profile-label">Name:</span>
                    <span class="profile-value"><?php echo htmlspecialchars($driver['name']); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Email:</span>
                    <span class="profile-value"><?php echo htmlspecialchars($driver['email']); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Phone:</span>
                    <span class="profile-value"><?php echo htmlspecialchars($driver['phone']); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Vehicle:</span>
                    <span class="profile-value">
                        <?php
                            echo htmlspecialchars($driver['vehicle_type']);
                            if (isset($driver['vehicle_name']) && $driver['vehicle_name'] !== '') {
                                echo ' - ' . htmlspecialchars($driver['vehicle_name']);
                            }
                        ?>
                    </span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Vehicle Number:</span>
                    <span class="profile-value"><?php echo htmlspecialchars($driver['vehicle_number']); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Status:</span>
                    <span class="profile-value"><?php echo htmlspecialchars($driver['approval_status']); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Member Since:</span>
                    <span class="profile-value"><?php echo date('M d, Y', strtotime($driver['created_at'])); ?></span>
                </div>
            </div>

            <!-- Edit Profile Button -->
            <div style="text-align:center; margin-top:1rem;">
                <a href="edit_driver_profile.php" class="book-btn" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:10px 28px;border-radius:25px;font-weight:600;text-decoration:none;display:inline-block;">Edit Profile</a>
            </div>
        </div>

        <!-- Booking History -->
        <div class="card booking-history">
            <h2>📋 Recent Bookings</h2>
            <?php if ($bookings->num_rows > 0): ?>
                <?php while ($booking = $bookings->fetch_assoc()): ?>
                    <div class="booking-item">
                        <div class="booking-header">
                            <span class="booking-id">Booking #<?php echo $booking['booking_id']; ?></span>
                            <span class="status <?php echo strtolower($booking['trip_status']); ?>">
                                <?php echo $booking['trip_status']; ?>
                            </span>
                        </div>
                        <div class="booking-details">
                            <div class="detail-item">
                                <strong>Customer:</strong> <?php echo htmlspecialchars($booking['customer_name']); ?>
                            </div>
                            <div class="detail-item">
                                <strong>Contact:</strong> <?php echo htmlspecialchars($booking['customer_phone']); ?>
                            </div>
                            <div class="detail-item">
                                <strong>From:</strong> <?php echo htmlspecialchars($booking['pickup_location']); ?>
                            </div>
                            <div class="detail-item">
                                <strong>To:</strong> <?php echo htmlspecialchars($booking['drop_location']); ?>
                            </div>
                            <div class="detail-item">
                                <strong>Vehicle:</strong> <?php echo htmlspecialchars($booking['vehicle_type']); ?>
                            </div>
                            <div class="detail-item">
                                <strong>Date:</strong> <?php echo date('M d, Y H:i', strtotime($booking['booking_time'])); ?>
                            </div>
                            <?php if ($booking['fare']): ?>
                                <div class="detail-item">
                                    <strong>Fare:</strong> ₹<?php echo number_format($booking['fare'], 2); ?>
                                    
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($booking['start_time'])): ?>
                                <div class="detail-item">
                                    <strong>Trip Started At:</strong> <?php echo date('M d, Y H:i', strtotime($booking['start_time'])); ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($booking['end_time'])): ?>
                                <div class="detail-item">
                                    <strong>Trip Completed At:</strong> <?php echo date('M d, Y H:i', strtotime($booking['end_time'])); ?>
                                </div>
                            <?php endif; ?>
                            <?php
if (!empty($booking['expected_eta_driver'])) {
    echo "<div><strong>Customer's Expected Driver ETA to Pickup:</strong> " . htmlspecialchars($booking['expected_eta_driver']) . "</div>";
}
?>
                        </div>
                        
                        <?php if ($booking['trip_status'] == 'Accepted'): ?>
    <form method="POST" style="margin-top:10px;">
        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
        <button type="submit" name="start_trip" style="background:#43e97b;color:#fff;padding:8px 18px;border:none;border-radius:8px;cursor:pointer;">Start Trip</button>
    </form>
<?php elseif ($booking['trip_status'] == 'Started'): ?>
    <form method="POST" style="margin-top:10px;">
        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
        <button type="submit" name="complete_trip" style="background:#ff6b6b;color:#fff;padding:8px 18px;border:none;border-radius:8px;cursor:pointer;">Trip Completed</button>
    </form>
<?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-bookings">
                    <p>No bookings yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Booking Notifications Section -->
        <div class="card">
            <h2>🚦 New Ride Requests</h2>
            <?php
            if ($pending_bookings_notifications->num_rows > 0) {
                while ($booking = $pending_bookings_notifications->fetch_assoc()) {
                    echo "<div class='booking-item' style='margin-bottom:1.5rem; border-left:4px solid #667eea; background:#f8f9fa; border-radius:10px; padding:1rem;'>";
                    echo "<div><strong>From:</strong> " . htmlspecialchars($booking['pickup_location']) . " <strong>To:</strong> " . htmlspecialchars($booking['drop_location']) . "</div>";
                    echo "<div><strong>Vehicle Type:</strong> " . htmlspecialchars($booking['vehicle_type']) . " | <strong>Requested At:</strong> " . date('M d, Y H:i', strtotime($booking['booking_time'])) . "</div>";
                    // Accept form with input fields
                    echo "<form method='POST' action='' style='margin-top:10px;display:flex;flex-wrap:wrap;gap:10px;align-items:center;'>
                            <input type='hidden' name='booking_id' value='{$booking['booking_id']}'>

                            <input type='number' name='distance' step='0.1' min='0' placeholder='Distance (km)' required style='width:110px;'>
                            <input type='number' name='rate_per_km' step='0.01' min='0' placeholder='Rate/km' required style='width:90px;'>
                            <button type='submit' name='accept_trip' class='accept-btn' style='background:linear-gradient(135deg,#43e97b,#38f9d7);color:#fff;border:none;padding:10px 24px;border-radius:20px;font-weight:600;cursor:pointer;'>Accept & Update</button>
                          </form>";
                    echo "</div>";
                } // <-- This closes the while loop
            } else {
                echo "<div style='color:#666;text-align:center;padding:1rem;'>No new ride requests for your vehicle type.</div>";
            } // <-- This closes the if block
            ?>
        </div>
    </div>
    <!-- Your Bookings Section -->
        <div class="card">
            <h2>📚 Your Bookings</h2>
            <div class="section" style="overflow-x:auto;">
    <h3>Your Bookings</h3>
    <table>
        <tr>
            <th>#</th>
            <th>Customer</th>
            <th>Pickup</th>
            <th>Drop</th>
            <th>Vehicle</th>
            <th>Status</th>
            <th>Time</th>
            <th>Fare</th>
        </tr>
        <?php
        $stmt = $conn->prepare("
            SELECT 
                b.*, 
                c.name AS customer_name
            FROM booking b
            LEFT JOIN customer c ON b.customer_id = c.customer_id
            WHERE b.driver_id = ?
            ORDER BY b.booking_time DESC
        ");
        $stmt->bind_param("s", $driver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $i = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$i}</td>
                <td>".htmlspecialchars($row['customer_name'])."</td>
                <td>".htmlspecialchars($row['pickup_location'])."</td>
                <td>".htmlspecialchars($row['drop_location'])."</td>
                <td>".htmlspecialchars($row['vehicle_type'])."</td>
                <td>".htmlspecialchars($row['trip_status'])."</td>
                <td>".date('M d, Y H:i', strtotime($row['booking_time']))."</td>
                <td>".(isset($row['fare']) ? '₹'.number_format($row['fare'],2) : '')."</td>
            </tr>";
            $i++;
        }
        ?>
    </table>
    
            </div>
        </div>
    </div>
    <?php if (!empty($error)): ?>
    <div class="error" style="color:#c00; margin-bottom:1rem;"><?php echo $error; ?></div>
<?php endif; ?>
</body>
</html>

</body>
</html>

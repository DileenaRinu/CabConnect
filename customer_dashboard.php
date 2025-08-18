<?php
session_start();
include 'db_connect.php';

$customer_id = $_SESSION['user_id'];

// Get customer details
$stmt = $conn->prepare("SELECT * FROM customer WHERE customer_id = ?");
$stmt->bind_param("s", $customer_id);
$stmt->execute();
$customer_details = $stmt->get_result()->fetch_assoc();
$customer_name = $customer_details['name'];

// Get recent bookings for this customer, with driver details if assigned
$stmt = $conn->prepare("
    SELECT 
        b.*, 
        d.name AS driver_name, 
        d.phone AS driver_phone, 
        d.vehicle_type AS driver_vehicle_type, 
        d.vehicle_number 
    FROM booking b
    LEFT JOIN driver d ON b.driver_id = d.driver_id
    WHERE b.customer_id = ?
    ORDER BY b.booking_time ASC
");
$stmt->bind_param("s", $customer_id);
$stmt->execute();
$bookings = $stmt->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_ride'])) {
    $pickup_location = trim($_POST["pickup_location"]);
    $drop_location = trim($_POST["drop_location"]);
    $vehicle_type = trim($_POST["vehicle_type"]);

    if (empty($pickup_location) || empty($drop_location) || empty($vehicle_type)) {
        $booking_error = "Please fill in all booking details";
    } else {
        $stmt = $conn->prepare("INSERT INTO booking (customer_id, pickup_location, drop_location, vehicle_type, booking_time, trip_status, driver_id) VALUES (?, ?, ?, ?, NOW(), 'Pending', NULL)");
        $stmt->bind_param("ssss", $customer_id, $pickup_location, $drop_location, $vehicle_type);
        if ($stmt->execute()) {
            $booking_success = "Your booking has been submitted! We're finding a driver for you.";
        } else {
            $booking_error = "Booking failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - CabConnect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f8f9fa;
            color: #333;
        }

        /* Header */
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

        .logo h1 {
            font-size: 2rem;
            font-weight: 700;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Main Container */
        .main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .card::before {
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

        .card h2 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        /* Booking Form */
        .booking-form {
            grid-column: 1 / -1;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .book-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            max-width: 300px;
        }

        .book-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        /* Alerts */
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Booking History */
        .booking-history {
            grid-column: 1 / -1;
        }

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

        .booking-id {
            font-weight: 600;
            color: #333;
        }

        .status {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status.accepted {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status.completed {
            background: #d4edda;
            color: #155724;
        }

        .status.cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .booking-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .detail-item {
            font-size: 0.9rem;
            color: #666;
        }

        .detail-item strong {
            color: #333;
        }

        /* Profile Card */
        .profile-info {
            display: grid;
            gap: 1rem;
        }

        .profile-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }

        .profile-item:last-child {
            border-bottom: none;
        }

        .profile-label {
            font-weight: 500;
            color: #666;
        }

        .profile-value {
            color: #333;
            font-weight: 600;
        }

        /* Stats Cards */
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
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .no-bookings {
            text-align: center;
            color: #666;
            padding: 2rem;
            font-style: italic;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-container {
                grid-template-columns: 1fr;
                padding: 0 1rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .booking-details {
                grid-template-columns: 1fr;
            }

            .header-container {
                padding: 0 1rem;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
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
                    <?php echo strtoupper(substr($customer_name, 0, 1)); ?>
                </div>
                <span>Welcome, <?php echo htmlspecialchars($customer_name); ?>!</span>
                <a href="logout_php.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <?php
            // Get total bookings
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM booking WHERE customer_id = ?");
            $stmt->bind_param("s", $customer_id);
            $stmt->execute();
            $total_bookings = $stmt->get_result()->fetch_assoc()['total'];

            // Get completed bookings
            $stmt = $conn->prepare("SELECT COUNT(*) as completed FROM booking WHERE customer_id = ? AND trip_status = 'Completed'");
            $stmt->bind_param("s", $customer_id);
            $stmt->execute();
            $completed_bookings = $stmt->get_result()->fetch_assoc()['completed'];

            // Get pending bookings
            $stmt = $conn->prepare("SELECT COUNT(*) as pending FROM booking WHERE customer_id = ? AND trip_status = 'Pending'");
            $stmt->bind_param("s", $customer_id);
            $stmt->execute();
            $pending_bookings = $stmt->get_result()->fetch_assoc()['pending'];
            ?>
            
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

        <!-- Book New Ride -->
        <div class="card booking-form">
            <h2>🚗 Book a New Ride</h2>
            
            <?php if (isset($booking_success)): ?>
                <div class="alert success"><?php echo $booking_success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($booking_error)): ?>
                <div class="alert error"><?php echo $booking_error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="pickup_location">Pickup Location</label>
                        <input type="text" id="pickup_location" name="pickup_location" required 
                               placeholder="Enter pickup address">
                    </div>
                    <div class="form-group">
                        <label for="drop_location">Drop Location</label>
                        <input type="text" id="drop_location" name="drop_location" required 
                               placeholder="Enter destination address">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="vehicle_type">Vehicle Type</label>
                    <select id="vehicle_type" name="vehicle_type" required>
                        <option value="">Select Vehicle Type</option>
                        <option value="car">Car</option>
                        <option value="scooty">Scooty</option>
                        <option value="Auto">Auto Rickshaw</option>
                        <option value="Bike">Bike</option>
                    </select>
                </div>
                
                <button type="submit" name="book_ride" class="book-btn">Book Ride Now</button>
            </form>
        </div>

        <!-- Profile Information -->
        <div class="card">
            <h2>👤 Your Profile</h2>
            <div class="profile-info">
                <div class="profile-item">
                    <span class="profile-label">Name:</span>
                    <span class="profile-value"><?php echo htmlspecialchars($customer_details['name']); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Email:</span>
                    <span class="profile-value"><?php echo htmlspecialchars($customer_details['email']); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Phone:</span>
                    <span class="profile-value"><?php echo htmlspecialchars($customer_details['phone']); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Member Since:</span>
                    <span class="profile-value"><?php echo date('M d, Y', strtotime($customer_details['created_at'])); ?></span>
                </div>
            </div>
        </div>

        <!-- Booking History -->
        <div class="card booking-history">
            <h2>📋 Recent Bookings</h2>
            
            <?php
            $booking_no = 1;
            if ($bookings->num_rows > 0): 
                while ($booking = $bookings->fetch_assoc()): ?>
                    <div class="booking-item">
                        <div class="booking-header">
                            <span class="booking-id">Booking #<?php echo $booking_no++; ?></span>
                            <span class="status <?php echo strtolower($booking['trip_status']); ?>">
                                <?php echo $booking['trip_status']; ?>
                            </span>
                        </div>
                        <div class="booking-details">
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
                            
                            <?php if (!empty($booking['driver_name'])): ?>
                                <div class="detail-item">
                                    <strong>Driver:</strong> <?php echo htmlspecialchars($booking['driver_name']); ?>
                                </div>
                                <div class="detail-item">
                                    <strong>Contact:</strong> <?php echo htmlspecialchars($booking['driver_phone']); ?>
                                </div>
                                <div class="detail-item">
                                    <strong>Driver Vehicle:</strong> <?php echo htmlspecialchars($booking['driver_vehicle_type']); ?>
                                </div>
                                <div class="detail-item">
                                    <strong>Vehicle No:</strong> <?php echo htmlspecialchars($booking['vehicle_number']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($booking['fare'])): ?>
                                <div class="detail-item">
                                    <strong>Fare:</strong> ₹<?php echo number_format($booking['fare'], 2); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($booking['driver_eta'])): ?>
                                <div class="detail-item">
                                    <strong>ETA:</strong> <?php echo htmlspecialchars($booking['driver_eta']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-bookings">
                    <p>No bookings yet. Book your first ride above!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Auto-refresh pending bookings every 30 seconds
        setInterval(function() {
            const pendingBookings = document.querySelectorAll('.status.pending');
            if (pendingBookings.length > 0) {
                location.reload();
            }
        }, 30000);

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const pickup = document.getElementById('pickup_location').value.trim();
            const drop = document.getElementById('drop_location').value.trim();
            const vehicle = document.getElementById('vehicle_type').value;
            
            if (!pickup || !drop || !vehicle) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    </script>
</body>
</html>
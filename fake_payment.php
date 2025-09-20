<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $amount = $_POST['amount'];
} else {
    // fallback for GET (optional)
    $booking_id = $_GET['booking_id'] ?? '';
    $amount = $_GET['amount'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CabConnect Payment</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
<h2>Pay for Booking #<?php echo htmlspecialchars($booking_id); ?></h2>
<p>Total Amount: ₹<?php echo number_format($amount, 2); ?></p>
<button id="payBtn">Pay with Razorpay</button>

<script>
document.getElementById('payBtn').onclick = function(e){
    var options = {
        "key": "rzp_test_RI9Dhur1or0JVO",
        "amount": "<?php echo intval($amount * 100); ?>",
        "currency": "INR",
        "name": "CabConnect",
        "description": "Booking Payment",
        "handler": function (response){
            // AJAX call to update payment_status in DB
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_payment_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert("Payment successful! Payment ID: " + response.razorpay_payment_id);
                    window.location.href = "customer_dashboard.php";
                }
            };
            xhr.send("booking_id=<?php echo $booking_id; ?>&payment_id=" + response.razorpay_payment_id);
        },
        "theme": {
            "color": "#43e97b"
        }
    };
    var rzp1 = new Razorpay(options);
    rzp1.open();
    e.preventDefault();
}
</script>
</body>
</html>
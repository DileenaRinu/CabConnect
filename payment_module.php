<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $amount = $_POST['amount'];
} else {
    $booking_id = $_GET['booking_id'] ?? '';
    $amount = $_GET['amount'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CabConnect | Secure Payment</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .payment-container {
            background: #fff;
            max-width: 400px;
            margin: 60px auto 0 auto;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(34, 49, 63, 0.15);
            padding: 32px 28px 28px 28px;
            text-align: center;
        }
        .payment-container h2 {
            color: #43e97b;
            margin-bottom: 12px;
        }
        .payment-container p {
            font-size: 1.1rem;
            color: #222;
            margin-bottom: 28px;
        }
        #payBtn {
            background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 14px 36px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(67, 233, 123, 0.15);
            transition: background 0.2s;
        }
        #payBtn:hover {
            background: linear-gradient(90deg, #38f9d7 0%, #43e97b 100%);
        }
        .razorpay-logo {
            width: 120px;
            margin-bottom: 18px;
        }
        .secure {
            color: #888;
            font-size: 0.95rem;
            margin-top: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .secure svg {
            color: #43e97b;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <img src="https://razorpay.com/favicon.png" alt="Razorpay" class="razorpay-logo">
        <h2>Secure Payment</h2>
        <p>Total Amount: <strong>₹<?php echo number_format($amount, 2); ?></strong></p>
        <button id="payBtn">Pay with Razorpay</button>
        <div class="secure">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2a7 7 0 0 0-7 7v4.28a2 2 0 0 1-.59 1.41l-1.3 1.3A2 2 0 0 0 5 20h14a2 2 0 0 0 1.89-2.59l-1.3-1.3A2 2 0 0 1 19 13.28V9a7 7 0 0 0-7-7Zm0 2a5 5 0 0 1 5 5v4.28c0 .53.21 1.04.59 1.41l1.3 1.3A.5.5 0 0 1 19 18H5a.5.5 0 0 1-.35-.85l1.3-1.3A2 2 0 0 0 7 13.28V9a5 5 0 0 1 5-5Zm0 7a1 1 0 1 0 0 2 1 1 0 0 0 0-2Z"></path></svg>
            100% Secure powered by Razorpay
        </div>
    </div>
    <script>
    document.getElementById('payBtn').onclick = function(e){
        var options = {
            "key": "rzp_test_RI9Dhur1or0JVO",
            "amount": "<?php echo intval($amount * 100); ?>",
            "currency": "INR",
            "name": "CabConnect",
            "description": "Booking Payment",
            "handler": function (response){
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "update_payment_status.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
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

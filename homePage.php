<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CabConnect - Your Reliable Taxi Booking Service</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 64px;
            margin-right: 10px;
        }

        .logo span {
            font-size: 2rem;
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 25px;
        }

        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .auth-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: white;
            color: #667eea;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: white;
            color: #667eea;
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero {
            position: relative;
            overflow: hidden;
            min-height: 600px; /* Increased height for large images */
            height: 90vh;
            color: white;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .hero-bg-slideshow {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .bg-slide {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            object-fit: cover;   /* <-- Change from contain to cover */
            opacity: 0;
            transition: opacity 1s;
            z-index: 0;
        }

        .bg-slide.active {
            opacity: 1;
            z-index: 1;
        }

        /* Overlay for better text readability */
        .hero-bg-slideshow::after {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(34,34,34,0.45);
            z-index: 2;
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 3;
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            padding: 3rem 2rem;
            background: rgba(34,34,34,0.15);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-large {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            border-radius: 30px;
        }

        /* Features Section */
        .features {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .section-title p {
            font-size: 1.1rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
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

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #667eea;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* How It Works Section */
        .how-it-works {
            padding: 80px 0;
            background: white;
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .step {
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 auto 1rem;
        }

        .step h3 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .step p {
            color: #666;
        }

        /* CTA Section */
        .cta {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .cta h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .cta p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        /* Footer */
        .footer {
            background: #333;
            color: white;
            padding: 40px 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            margin-bottom: 1rem;
            color: #667eea;
        }

        .footer-section p, .footer-section a {
            color: #ccc;
            text-decoration: none;
            line-height: 1.6;
        }

        .footer-section a:hover {
            color: #667eea;
        }

        .footer-bottom {
            border-top: 1px solid #555;
            padding-top: 20px;
            text-align: center;
            color: #ccc;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .steps {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="nav-container">
            <div class="logo">
                <img src="logo.png" alt="CabConnect Logo" style="height:48px; vertical-align:middle; margin-right:10px;">
                <span style="font-size:2rem; font-weight:700; vertical-align:middle;">CabConnect</span>
            </div>
            <div class="nav-links">
                <a href="#home">Home</a>
                <a href="#features">Features</a>
                <a href="#how-it-works">How It Works</a>
                <a href="#contact">Contact</a>
            </div>
            <div class="auth-buttons">
                <a href="unified_login.php" class="btn btn-secondary">Sign In</a>
                <a href="unified_register.php" class="btn btn-primary">Register</a>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-bg-slideshow">
            <img src="car1.png" class="bg-slide active" alt="Cab 1">
            <img src="car4.jpg" class="bg-slide" alt="Cab 2">
            <img src="car3.jpg" class="bg-slide" alt="Cab 2">
            <img src="car2.jpg" class="bg-slide" alt="Cab 3">
        </div>
        <div class="hero-content">
            <h1>Your Ride, Your Way</h1>
            <p>Safe, reliable, and affordable taxi booking service at your fingertips. Book your ride in seconds and reach your destination comfortably.</p>
            <div class="hero-buttons">
                <a href="#how-it-works" class="btn btn-secondary btn-large">Learn More</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Why Choose CabConnect?</h2>
                <p>Experience the best taxi booking service with our innovative features designed for your convenience</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🚗</div>
                    <h3>Quick Booking</h3>
                    <p>Book your ride in just a few clicks. Our streamlined process gets you moving fast.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h3>Safe & Secure</h3>
                    <p>All our drivers are verified and trained. Your safety is our top priority.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3>Affordable Prices</h3>
                    <p>Competitive rates with no hidden charges. Pay what you see upfront.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3>Real-time Tracking</h3>
                    <p>Track your ride in real-time and know exactly when your driver will arrive.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⭐</div>
                    <h3>Quality Service</h3>
                    <p>Highly rated drivers and well-maintained vehicles for your comfort.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🕒</div>
                    <h3>24/7 Availability</h3>
                    <p>Need a ride anytime? We're here for you round the clock, every day.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works" id="how-it-works">
        <div class="container">
            <div class="section-title">
                <h2>How It Works</h2>
                <p>Getting your ride is simple and straightforward</p>
            </div>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Sign Up</h3>
                    <p>Create your account in seconds with just your basic information</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Book Your Ride</h3>
                    <p>Enter your pickup and destination, choose your vehicle type</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Get Matched</h3>
                    <p>We'll find the nearest available driver for your trip</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Enjoy Your Ride</h3>
                    <p>Relax and enjoy your comfortable journey to your destination</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Ready to Get Started?</h2>
            <p>Join thousands of satisfied customers who trust CabConnect for their transportation needs</p>
            <div class="hero-buttons">
                <a href="unified_register.php" class="btn btn-primary btn-large">Register Now</a>
                <a href="unified_login.php" class="btn btn-secondary btn-large">Sign In</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>CabConnect</h3>
                    <p>Your reliable taxi booking service. We connect you with safe, affordable, and convenient transportation solutions.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <p><a href="#home">Home</a></p>
                    <p><a href="#features">Features</a></p>
                    <p><a href="#how-it-works">How It Works</a></p>
                    <p><a href="unified_register.php">Register</a></p>
                </div>
                <div class="footer-section">
                    <h3>For Drivers</h3>
                    <p><a href="driver_register.php">Become a Driver</a></p>
                    <p><a href="unified_login.php">Driver Login</a></p>
                    <p><a href="#contact">Driver Support</a></p>
                </div>
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <p>Email: support@cabconnect.com</p>
                    <p>Phone: +91 9876543210</p>
                    <p>Address: 123 Tech Street, Chennai, TN</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 CabConnect. All rights reserved. | A BCA Mini Project</p>
            </div>
        </div>
    </footer>

    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Header background on scroll
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(102, 126, 234, 0.95)';
            } else {
                header.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            }
        });

        // Slideshow for hero background
        const slides = document.querySelectorAll('.bg-slide');
        let slideIndex = 0;
        function showSlide(idx) {
            slides.forEach((img, i) => img.classList.toggle('active', i === idx));
        }
        setInterval(() => {
            slideIndex = (slideIndex + 1) % slides.length;
            showSlide(slideIndex);
        }, 3000);
        showSlide(slideIndex);
    </script>
</body>
</html>
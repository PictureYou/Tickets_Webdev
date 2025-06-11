<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/home_style.css">
    <title>Skywings</title>
    <style>
        #destinationGrid {
            overflow-x: hidden !important;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="nav__header">
                <div class="nav__logo">
                    <a href="index.php" class="logo">Skywings</a>
                </div>
                <div class="nav__menu__btn" id="menu-btn">
                    <i class="ri-menu-line"></i>
                </div>
            </div>
            <ul class="nav__links" id="nav-links">
                <li><a href="index.php">HOME</a></li>
                <li><a href="booking.php">BOOK TRIP</a></li>
                <?php if (isset($_SESSION['id'])): ?>
                    <li><a href="tickets.php">BOOKED FLIGHTS</a></li>
                    <li><a href="logout.php">LOGOUT</a></li>
                <?php else: ?>
                    <li><a href="login.php">LOGIN</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <?php echo $content ?? ''; ?>
    </main>
    <footer id="contact">
        <div class="section__container footer__container">
            <div class="footer__col">
            <div class="footer__logo">
                <a href="#" class="logo">Skywings</a>
            </div>
            <p>
                Explore the world with ease and excitement through our comprehensive
                travel platform. Your journey begins here, where seamless planning
                meets unforgettable experiences.
            </p>
            </div>
            <div class="footer__col">
            <h4>Quick Links</h4>
            <ul class="footer__links">
                <li><a href="index.php">Home</a></li>
                <li><a href="booking.php">Book a Trip</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="adminlogin.php">Admin Login</a></li>
            </ul>
            </div>
            <div class="footer__col">
            <h4>Contact Us</h4>
            <ul class="footer__links">
                <li>
                <a href="#">
                    <span><i class="ri-phone-fill"></i></span> +12 34567890
                </a>
                </li>
                <li>
                <a href="#">
                    <span><i class="ri-record-mail-line"></i></span> info@skywings
                </a>
                </li>
                <li>
                <a href="#">
                    <span><i class="ri-map-pin-2-fill"></i></span> Mcc
                </a>
                </li>
            </ul>
            </div>
        </div>
        <div class="footer__bar">
            Copyright © 2024 Web Design Mastery. All rights reserved.
        </div>
    </footer>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const grid = document.getElementById('destinationGrid');
        const leftBtn = document.getElementById('slide-left');
        const rightBtn = document.getElementById('slide-right');
        if (leftBtn && rightBtn && grid) {
            leftBtn.onclick = () => grid.scrollBy({left: -300, behavior: 'smooth'});
            rightBtn.onclick = () => grid.scrollBy({left: 300, behavior: 'smooth'});
        }
    });
    </script>
</body>
</html>
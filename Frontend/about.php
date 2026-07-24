<?php
session_start();
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - SEN'Q Restaurant</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;400;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4a085e9b53.js" crossorigin="anonymous"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #fefcf3; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .btn { display: inline-block; background: #b45f2b; color: #fff; padding: 12px 30px; border-radius: 40px; font-weight: 600; border: none; cursor: pointer; transition: 0.3s; font-size: 1rem; text-decoration: none; }
        .btn:hover { background: #8a471f; transform: scale(1.03); }
        
        .top-bar { background: #1a1a1a; color: #eee; padding: 8px 0; }
        .top-bar .flex { display: flex; justify-content: flex-end; gap: 15px; align-items: center; flex-wrap: wrap; }
        .top-bar a { color: #ddd; font-weight: 500; font-size: 0.95rem; text-decoration: none; }
        .top-bar a:hover { color: #f1c40f; }
        .top-bar .welcome { color: #f1c40f; margin-right: 10px; }
        
        .main-header { background: #2c2c2c; padding: 12px 0; border-bottom: 3px solid #b45f2b; }
        .main-header .flex { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .logo-area { display: flex; align-items: center; gap: 12px; }
        .logo-icon { background: #b45f2b; color: #fff; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; }
        .logo-area h1 { font-size: 2rem; color: #f1c40f; letter-spacing: 2px; }
        .nav-links { display: flex; gap: 28px; flex-wrap: wrap; }
        .nav-links a { color: #f0f0f0; font-weight: 500; font-size: 1.05rem; padding: 6px 0; border-bottom: 2px solid transparent; transition: 0.2s; text-decoration: none; }
        .nav-links a:hover, .nav-links a.active { border-bottom-color: #f1c40f; color: #f1c40f; }
        
        .footer { background: #1e1a16; color: #d6cec4; padding: 50px 0 20px; margin-top: 40px; }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 30px; }
        .footer-grid h4 { color: #f1c40f; margin-bottom: 16px; font-size: 1.2rem; }
        .footer-grid ul { list-style: none; }
        .footer-grid ul li { margin-bottom: 8px; }
        .footer-grid ul li a { color: #d6cec4; text-decoration: none; }
        .footer-grid ul li a:hover { color: #f1c40f; }
        .footer-bottom { border-top: 1px solid #3d322a; margin-top: 40px; padding-top: 20px; text-align: center; font-size: 0.9rem; color: #a3978a; }
        .social-icons { display: flex; gap: 12px; font-size: 1.8rem; }
        .social-icons a { color: #d6cec4; transition: 0.3s; }
        .social-icons a:hover { color: #f1c40f; }
        
        .section-title { text-align: center; font-size: 2.4rem; font-weight: 600; margin: 50px 0 30px; color: #2c1f16; position: relative; }
        .section-title:after { content: ''; display: block; width: 80px; height: 4px; background: #b45f2b; margin: 10px auto 0; }
        
        .about-page { padding: 40px 0; }
        .about-hero { background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), #2c1f16; padding: 80px 20px; text-align: center; color: #fff; border-radius: 20px; margin-bottom: 40px; }
        .about-hero h1 { font-size: 3rem; margin-bottom: 10px; }
        .about-hero h1 span { color: #f1c40f; }
        .about-hero p { font-size: 1.2rem; max-width: 700px; margin: 0 auto; color: #ddd; }
        .about-content { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; }
        .about-content .text h2 { font-size: 2rem; color: #2c1f16; margin-bottom: 20px; }
        .about-content .text p { color: #555; margin-bottom: 15px; line-height: 1.8; }
        .about-content .image { background: #e6dccc; height: 400px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 80px; color: #b45f2b; }
        .values-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-top: 40px; }
        .value-card { background: #fff; padding: 30px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); text-align: center; border: 1px solid #eee; transition: 0.3s; }
        .value-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .value-card i { font-size: 3rem; color: #b45f2b; margin-bottom: 15px; }
        .value-card h4 { font-size: 1.2rem; color: #2c1f16; margin-bottom: 10px; }
        .value-card p { color: #666; font-size: 0.95rem; }
        .team-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; margin-top: 40px; }
        .team-card { background: #fff; padding: 25px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); text-align: center; border: 1px solid #eee; }
        .team-card .avatar { width: 100px; height: 100px; border-radius: 50%; background: #b45f2b; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #fff; font-weight: bold; }
        .team-card h4 { color: #2c1f16; }
        .team-card p { color: #999; font-size: 0.85rem; }
        
        @media (max-width: 800px) { 
            .about-content { grid-template-columns: 1fr; }
            .about-hero h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="container">
        <div class="flex">
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="welcome"><i class="fas fa-user-circle"></i> Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Log in</a>
                <a href="signup.php"><i class="fas fa-user-plus"></i> Sign up</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<header class="main-header">
    <div class="container">
        <div class="flex">
            <div class="logo-area">
                <div class="logo-icon">S</div>
                <h1>SEN'Q</h1>
            </div>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="menu.php">Menu</a>
                <a href="booking.php">Book Table</a>
                <a href="order.php">Order</a>
                <a href="contact.php">Contact</a>
                <a href="about.php" class="active">About</a>
            </nav>
        </div>
    </div>
</header>

<section class="about-page">
    <div class="container">
        <div class="about-hero">
            <h1>About <span>SEN'Q</span></h1>
            <p>Discover the story behind our traditional Ethiopian restaurant</p>
        </div>

        <div class="about-content">
            <div class="text">
                <h2>Our Story</h2>
                <p>The word <strong>SEN'Q</strong> is a collection of food put in a special container called <strong>AGELGIL</strong>. It is mainly practiced in the rural areas of Ethiopia to deliver food for farmers specifically who work in a team for crop gathering.</p>
                <p>At lunchtime, their wives come with food inside Agelgil which is called <strong>SEN'Q</strong> and also with a traditional drink called Tela. Not only for farmers, it is also used for a person who will travel long way by foot. When he gets tired, he will rest and eat his SEN'Q whose mother or wife made it.</p>
                <p>We bring that warmth to your table, every day. Our restaurant is dedicated to preserving this beautiful Ethiopian tradition and sharing it with the world.</p>
                <a href="menu.php" class="btn" style="margin-top:20px;">Explore Our Menu</a>
            </div>
            <div class="image">
                <i class="fas fa-utensils"></i>
            </div>
        </div>

        <h2 class="section-title">Our Values</h2>
        <div class="values-grid">
            <div class="value-card">
                <i class="fas fa-heart"></i>
                <h4>Authenticity</h4>
                <p>We stay true to traditional Ethiopian recipes and cooking methods.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-users"></i>
                <h4>Community</h4>
                <p>We believe in bringing people together through food and shared experiences.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-leaf"></i>
                <h4>Quality</h4>
                <p>We use only the freshest ingredients to ensure the best taste.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-smile"></i>
                <h4>Hospitality</h4>
                <p>We treat every guest like family with warm Ethiopian hospitality.</p>
            </div>
        </div>

        <h2 class="section-title">Meet Our Team</h2>
        <div class="team-grid">
            <div class="team-card">
                <div class="avatar">D</div>
                <h4>Dibora</h4>
                <p>Head Chef</p>
            </div>
            <div class="team-card">
                <div class="avatar">E</div>
                <h4>Edelawit</h4>
                <p>Manager</p>
            </div>
            <div class="team-card">
                <div class="avatar">E</div>
                <h4>Eden</h4>
                <p>Lead Server</p>
            </div>
            <div class="team-card">
                <div class="avatar">E</div>
                <h4>Eftu</h4>
                <p>Chef</p>
            </div>
            <div class="team-card">
                <div class="avatar">H</div>
                <h4>Hawi</h4>
                <p>Marketing</p>
            </div>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <h4>Company</h4>
                <ul>
                    <li><a href="about.php">About us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="menu.php">Menu</a></li>
                    <li><a href="order.php">Order</a></li>
                </ul>
            </div>
       
            <div>
                <h4>Contact</h4>
                <ul>
                    <li><i class="fas fa-phone"></i> +251924950125</li>
                    <li><i class="fas fa-map-marker-alt"></i> Addis Ababa</li>
                </ul>
            </div>
            <div>
                <h4>Follow</h4>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-telegram"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?php echo date('Y'); ?> SEN'Q Restaurant. All rights reserved.
        </div>
    </div>
</footer>

</body>
</html>
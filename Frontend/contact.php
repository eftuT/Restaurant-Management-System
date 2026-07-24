<?php
session_start();
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if(empty($name) || empty($email) || empty($message)) {
        $error = 'Name, email and message are required';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {
        $stmt = $conn->prepare("INSERT INTO contact (fullname, phoneno, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $phone, $email);
        if($stmt->execute()) {
            $success = 'Message sent successfully! We will get back to you soon.';
        } else {
            $error = 'Failed to send message. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - SEN'Q Restaurant</title>
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
        
        .contact-page { padding: 40px 0; }
        .contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; background: #f7f2e9; padding: 50px 40px; border-radius: 30px; }
        .contact-info h3 { font-size: 1.8rem; color: #2c1f16; margin-bottom: 20px; }
        .contact-info p { padding: 8px 0; font-size: 1.1rem; }
        .contact-info i { width: 30px; color: #b45f2b; }
        .contact-form input, .contact-form textarea { width: 100%; padding: 14px 18px; border: 2px solid #ddd; border-radius: 10px; font-size: 1rem; margin-bottom: 18px; background: #fff; transition: 0.3s; }
        .contact-form input:focus, .contact-form textarea:focus { border-color: #b45f2b; outline: none; box-shadow: 0 0 0 3px rgba(180,95,43,0.1); }
        .contact-form textarea { border-radius: 10px; min-height: 120px; resize: vertical; }
        .contact-form .btn { width: 100%; padding: 14px; border: none; border-radius: 10px; background: linear-gradient(135deg, #b45f2b, #8a471f); color: #fff; font-weight: 600; font-size: 16px; cursor: pointer; transition: 0.3s; }
        .contact-form .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(180,95,43,0.3); }
        .contact-form .error { background: #fee; color: #c00; padding: 12px; border-radius: 10px; margin-bottom: 15px; text-align: center; border-left: 4px solid #c00; }
        .contact-form .success { background: #efe; color: #060; padding: 12px; border-radius: 10px; margin-bottom: 15px; text-align: center; border-left: 4px solid #060; }
        @media (max-width: 800px) { .contact-grid { grid-template-columns: 1fr; padding: 30px 20px; } }
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
                <a href="contact.php" class="active">Contact</a>
            </nav>
        </div>
    </div>
</header>

<section class="contact-page">
    <div class="container">
        <h2 class="section-title">Contact Us</h2>
        <div class="contact-grid">
            <div class="contact-info">
                <h3>Get in touch</h3>
                <p><i class="fas fa-phone-alt"></i> +251 924 950 125</p>
                <p><i class="fas fa-phone-alt"></i> +251 924 950 125</p>
                <p><i class="fas fa-map-marker-alt"></i> Ethiopia, Addis Ababa, Kasaches</p>
                <p><i class="fas fa-clock"></i> Mon–Sun: 7:00 AM – 9:00 PM</p>
                
                <div style="margin-top:30px;">
                    <h4 style="color:#2c1f16;margin-bottom:10px;">Follow us</h4>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-telegram"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                
            </div>

            <div class="contact-form">
                <?php if($error): ?>
                    <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <input type="text" name="name" placeholder="Your name" required>
                    <input type="email" name="email" placeholder="Your email" required>
                    <input type="tel" name="phone" placeholder="Your phone">
                    <textarea name="message" placeholder="Message..." rows="5" required></textarea>
                    <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Send message</button>
                </form>
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
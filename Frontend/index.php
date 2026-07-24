<?php
session_start();
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

// Check if image_url column exists, if not add it
$checkColumn = $conn->query("SHOW COLUMNS FROM food LIKE 'image_url'");
if($checkColumn->num_rows == 0) {
    $conn->query("ALTER TABLE food ADD COLUMN image_url VARCHAR(255) AFTER food_description");
}

$featured = $conn->query("SELECT * FROM food WHERE is_available = 1 LIMIT 4");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - SEN'Q Restaurant</title>
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
        
        .hero { background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.7)), #2c1f16; min-height: 70vh; display: flex; align-items: center; justify-content: center; text-align: center; color: #fff; padding: 40px 20px; }
        .hero-content h1 { font-size: 3.8rem; font-weight: 700; text-shadow: 3px 3px 8px rgba(0,0,0,0.7); }
        .hero-content p { font-size: 1.5rem; max-width: 700px; margin: 18px auto 30px; color: #f5e6c8; }
        .about-section { padding: 60px 20px; background: #f7f2e9; }
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; max-width: 1100px; margin: 0 auto; }
        .about-text h2 { font-size: 2.5rem; color: #2c1f16; }
        .about-text p { font-size: 1.1rem; color: #3d3d3d; margin-top: 15px; }
        
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px; }
        .menu-card { background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); text-align: center; border: 1px solid #eee; transition: 0.3s; }
        .menu-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .menu-card .menu-image { width: 100%; height: 200px; overflow: hidden; background: #e6dccc; display: flex; align-items: center; justify-content: center; }
        .menu-card .menu-image img { width: 100%; height: 100%; object-fit: cover; }
        .menu-card .menu-image .placeholder { font-size: 60px; color: #b45f2b; }
        .menu-card .info { padding: 16px; }
        .menu-card h4 { font-size: 1.2rem; color: #2c1f16; }
        .menu-card .desc { font-size: 0.85rem; color: #666; margin: 4px 0 8px; }
        .menu-card .price { color: #b45f2b; font-weight: 700; font-size: 1.1rem; margin: 6px 0 12px; }
        
        .services-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 25px; max-width: 1000px; margin: 0 auto; padding: 40px 0; }
        .service-item { background: #fff; padding: 25px; border-radius: 20px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #eee; }
        .service-item i { font-size: 2.5rem; color: #b45f2b; margin-bottom: 10px; }
        .service-item h4 { color: #2c1f16; }
        
        @media (max-width: 800px) { .about-grid { grid-template-columns: 1fr; } .hero-content h1 { font-size: 2.8rem; } }
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
                <a href="index.php" class="active">Home</a>
                <a href="menu.php">Menu</a>
                <a href="booking.php">Book Table</a>
                <a href="reservation.php">Reservation</a>
                <a href="order.php">Order</a>
                <a href="contact.php">Contact</a>
            </nav>
        </div>
    </div>
</header>

<section class="hero">
    <div class="hero-content">
        <h1>Welcome to SEN'Q</h1>
        <p>Traditional Ethiopian flavors — served with love, just like home.</p>
        <a href="menu.php" class="btn">Explore Menu</a>
    </div>
</section>

<section class="about-section">
    <div class="about-grid">
        <div class="about-text">
            <h2>About SEN'Q</h2>
            <p><strong>SEN'Q</strong> is a collection of food in a traditional container called <em>Agelgil</em>. It's the meal shared by farmers during harvest, by travelers on long journeys — a symbol of togetherness and Ethiopian heritage.</p>
            <p style="margin-top:12px;">We bring that warmth to your table, every day.</p>
            <a href="booking.php" class="btn" style="margin-top:20px;display:inline-block;">Book a Table</a>
        </div>
        <div class="about-image">
            <div style="background:#e6dccc;height:300px;border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:80px;color:#b45f2b;">
                <i class="fas fa-utensils"></i>
            </div>
        </div>
    </div>
</section>

<section style="padding:60px 20px;">
    <div class="container">
        <h2 class="section-title">Our Menu</h2>
        <div class="menu-grid">
            <?php while($row = $featured->fetch_assoc()): 
                // Check if image exists
                $imagePath = '';
                if(!empty($row['image_url'])) {
                    $fullPath = __DIR__ . '/../' . $row['image_url'];
                    if(file_exists($fullPath)) {
                        $imagePath = '/Restaurant-Management-System/' . $row['image_url'];
                    }
                }
            ?>
            <div class="menu-card">
                <div class="menu-image">
                    <?php if($imagePath): ?>
                        <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($row['food_name']); ?>">
                    <?php else: ?>
                        <div class="placeholder"><i class="fas fa-utensils"></i></div>
                    <?php endif; ?>
                </div>
                <div class="info">
                    <h4><?php echo htmlspecialchars($row['food_name']); ?></h4>
                    <p class="desc"><?php echo htmlspecialchars($row['food_description'] ?? 'Delicious Ethiopian dish'); ?></p>
                    <div class="price"><?php echo $row['food_price']; ?> Br</div>
                    <a href="order.php?item=<?php echo $row['id']; ?>" class="btn" style="font-size:0.9rem;padding:8px 22px;">Order Now</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div style="text-align:center;margin-top:30px;">
            <a href="menu.php" class="btn">View Full Menu</a>
        </div>
    </div>
</section>

<section style="padding:40px 0;background:#f7f2e9;">
    <div class="container">
        <h2 class="section-title">Our Services</h2>
        <div class="services-grid">
            <div class="service-item">
                <i class="fas fa-utensils"></i>
                <h4>Dine-in</h4>
                <p style="color:#666;font-size:0.9rem;">Enjoy at our restaurant</p>
            </div>
            <div class="service-item">
                <i class="fas fa-truck"></i>
                <h4>Delivery</h4>
                <p style="color:#666;font-size:0.9rem;">Food to your door</p>
            </div>
            <div class="service-item">
                <i class="fas fa-shopping-bag"></i>
                <h4>Takeaway</h4>
                <p style="color:#666;font-size:0.9rem;">Order and pick up</p>
            </div>
            <div class="service-item">
                <i class="fas fa-calendar-check"></i>
                <h4>Reservation</h4>
                <p style="color:#666;font-size:0.9rem;">Book your table</p>
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
                <h4>Owners</h4>
                <ul>
                    <li>Dibora</li>
                    <li>Edelawit</li>
                    <li>Eden</li>
                    <li>Eftu</li>
                    <li>Hawi</li>
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
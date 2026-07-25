<?php
session_start();
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

// Check if image_url column exists, if not add it
$checkColumn = $conn->query("SHOW COLUMNS FROM food LIKE 'image_url'");
if($checkColumn->num_rows == 0) {
    $conn->query("ALTER TABLE food ADD COLUMN image_url VARCHAR(255) AFTER food_description");
}

$menuItems = $conn->query("SELECT * FROM food WHERE is_available = 1 ORDER BY 
    FIELD(food_category, 'breakfast', 'lunch', 'dinner', 'beverage', 'snack', 'dessert'), 
    food_name");

$categories = [];
while($row = $menuItems->fetch_assoc()) {
    $categories[$row['food_category']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - SEN'Q Restaurant</title>
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
        
        .menu-page { padding: 40px 0; }
        .menu-page h1 { text-align: center; font-size: 2.8rem; color: #2c1f16; margin-bottom: 10px; }
        .menu-page .subtitle { text-align: center; color: #666; margin-bottom: 40px; }
        .category-section { margin-bottom: 50px; }
        .category-title { 
            font-size: 2rem; 
            color: #2c1f16; 
            padding-bottom: 10px; 
            border-bottom: 3px solid #b45f2b; 
            margin-bottom: 25px; 
            text-transform: capitalize; 
            display: flex; 
            align-items: center; 
            gap: 12px;
        }
        .category-title i { 
            color: #b45f2b; 
            font-size: 1.8rem; 
        }
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
        .no-items { text-align: center; padding: 40px; color: #666; }
        .no-items i { font-size: 50px; color: #ddd; display: block; margin-bottom: 20px; }
        
        @media (max-width: 800px) { .menu-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 500px) { .menu-grid { grid-template-columns: 1fr; } }
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
                <a href="menu.php" class="active">Menu</a>
                <a href="booking.php">Book Table</a>
                <a href="reservation.php">Reservation</a>
                <a href="order.php">Order</a>
                <a href="contact.php">Contact</a>
            </nav>
        </div>
    </div>
</header>

<section class="menu-page">
    <div class="container">
        <h1>Our Menu</h1>
        <p class="subtitle">Authentic Ethiopian flavors made with love</p>

        <?php if(empty($categories)): ?>
            <div class="no-items">
                <i class="fas fa-utensils"></i>
                <h3>No menu items available</h3>
                <p>Please check back later or contact us for more information.</p>
            </div>
        <?php else: ?>
            <?php 
            // Define category icons
            $categoryIcons = [
                'breakfast' => 'fa-sun',
                'lunch' => 'fa-utensils',
                'dinner' => 'fa-moon',
                'beverage' => 'fa-mug-hot',
                'snack' => 'fa-bolt',
                'dessert' => 'fa-cake'
            ];
            
            // Define category display names
            $categoryNames = [
                'breakfast' => 'Breakfast',
                'lunch' => 'Lunch',
                'dinner' => 'Dinner',
                'beverage' => 'Beverages',
                'snack' => 'Snacks',
                'dessert' => 'Desserts'
            ];
            
            // Display categories in the specified order
            $order = ['breakfast', 'lunch', 'dinner', 'beverage', 'snack', 'dessert'];
            
            foreach($order as $category): 
                if(isset($categories[$category]) && !empty($categories[$category])):
            ?>
            <div class="category-section">
                <h3 class="category-title">
                    <i class="fas <?php echo $categoryIcons[$category] ?? 'fa-utensils'; ?>"></i>
                    <?php echo $categoryNames[$category] ?? ucfirst($category); ?>
                </h3>
                <div class="menu-grid">
                    <?php foreach($categories[$category] as $item): 
                        // Build image path
                        $imageSrc = '';
                        if(!empty($item['image_url'])) {
                            $imageUrl = ltrim($item['image_url'], '/');
                            $fullPath = __DIR__ . '/../' . $imageUrl;
                            if(file_exists($fullPath)) {
                                $imageSrc = '/Restaurant-Management-System/' . $imageUrl;
                            }
                        }
                    ?>
                    <div class="menu-card">
                        <div class="menu-image">
                            <?php if($imageSrc): ?>
                                <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($item['food_name']); ?>">
                            <?php else: ?>
                                <div class="placeholder"><i class="fas fa-utensils"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="info">
                            <h4><?php echo htmlspecialchars($item['food_name']); ?></h4>
                            <p class="desc"><?php echo htmlspecialchars($item['food_description'] ?? 'Delicious Ethiopian dish'); ?></p>
                            <div class="price"><?php echo $item['food_price']; ?> Br</div>
                            <a href="order.php?item=<?php echo $item['id']; ?>" class="btn" style="font-size:0.9rem;padding:8px 22px;">Order Now</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php 
                endif; 
            endforeach; 
            ?>
        <?php endif; ?>
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
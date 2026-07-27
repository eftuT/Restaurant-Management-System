<?php
// ============================================================
// SESSION IS ALREADY STARTED IN HEADER.PHP
// DO NOT START SESSION HERE AGAIN!
// ============================================================
require_once 'includes/header.php';
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

// Check if image_url column exists
$checkColumn = $conn->query("SHOW COLUMNS FROM food LIKE 'image_url'");
if($checkColumn->num_rows == 0) {
    $conn->query("ALTER TABLE food ADD COLUMN image_url VARCHAR(255) AFTER food_description");
}

$featured = $conn->query("SELECT * FROM food WHERE is_available = 1 LIMIT 4");
?>

<!-- HERO -->
<section class="hero">
    <div class="hero-content">
        <div class="hero-badge">✦ Authentic Ethiopian Cuisine</div>
        <h1>Welcome to <br><span>SEN'Q</span> Restaurant</h1>
        <p class="subtitle">Traditional Ethiopian flavors — served with love, just like home. Explore our authentic dishes made from the freshest ingredients.</p>
        <div class="btn-group">
            <a href="menu.php" class="btn"><i class="fas fa-utensils"></i> Explore Menu</a>
            <a href="booking.php" class="btn btn-outline"><i class="fas fa-calendar-check"></i> Book a Table</a>
        </div>
    </div>
</section>

<!-- MENU PREVIEW -->
<section style="padding:60px 20px;">
    <div class="container">
        <h2 class="section-title">Our Menu</h2>
        <div class="menu-grid">
            <?php while($row = $featured->fetch_assoc()): 
                $imageSrc = '';
                if(!empty($row['image_url'])) {
                    $imagePath = __DIR__ . '/../' . $row['image_url'];
                    if(file_exists($imagePath)) {
                        $imageSrc = '/Restaurant-Management-System/' . $row['image_url'];
                    }
                }
            ?>
            <div class="menu-card">
                <div class="menu-image">
                    <?php if($imageSrc): ?>
                        <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($row['food_name']); ?>">
                    <?php else: ?>
                        <div class="placeholder"><i class="fas fa-utensils"></i></div>
                    <?php endif; ?>
                </div>
                <div class="info">
                    <h4><?php echo htmlspecialchars($row['food_name']); ?></h4>
                    <p class="desc"><?php echo htmlspecialchars($row['food_description'] ?? 'Delicious Ethiopian dish'); ?></p>
                    <div class="price"><?php echo $row['food_price']; ?> Br</div>
                    <a href="order.php?add_to_cart=<?php echo $row['id']; ?>" class="btn" style="font-size:0.9rem;padding:8px 22px;">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div style="text-align:center;margin-top:30px;">
            <a href="menu.php" class="btn">View Full Menu</a>
        </div>
    </div>
</section>

<!-- SERVICES -->
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

<?php require_once 'includes/footer.php'; ?>
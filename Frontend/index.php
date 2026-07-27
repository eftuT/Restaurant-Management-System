<?php

require_once 'includes/header.php';
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

$checkColumn = $conn->query("SHOW COLUMNS FROM food LIKE 'image_url'");
if($checkColumn->num_rows == 0) {
    $conn->query("ALTER TABLE food ADD COLUMN image_url VARCHAR(255) AFTER food_description");
}

$featured = $conn->query("SELECT * FROM food WHERE is_available = 1 LIMIT 4");
?>

<style>
    /* Hero Section - Minimized */
    .hero {
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 40px 20px;
        background: linear-gradient(135deg, #f7f2e9 0%, #fefcf3 50%, #f5ede3 100%);
        position: relative;
        overflow: hidden;
        margin-bottom: 0;
    }
    .hero-content {
        max-width: 750px;
        padding: 20px;
    }
    .hero-badge {
        display: inline-block;
        background: #b45f2b;
        color: #fff;
        padding: 6px 20px;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 15px;
        text-transform: uppercase;
    }
    .hero h1 {
        font-size: 2.8rem;
        font-weight: 800;
        color: #2c1f16;
        line-height: 1.2;
        margin-bottom: 12px;
    }
    .hero h1 span {
        color: #b45f2b;
    }
    .hero .subtitle {
        font-size: 1rem;
        color: #666;
        max-width: 600px;
        margin: 0 auto 25px;
        line-height: 1.7;
    }
    .btn-group {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }
    .btn-group .btn {
        padding: 10px 28px;
        font-size: 0.9rem;
    }
    .btn-outline {
        background: transparent;
        border: 2px solid #b45f2b;
        color: #b45f2b;
    }
    .btn-outline:hover {
        background: #b45f2b;
        color: #fff;
    }
    
    /* Menu Preview - Minimized */
    .menu-preview {
        padding: 30px 20px 40px 20px;
    }
    .menu-preview .section-title {
        margin-top: 0;
        margin-bottom: 25px;
        text-align: center;
        font-size: 2rem;
        color: #2c1f16;
        position: relative;
        padding-bottom: 15px;
    }
    .menu-preview .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: #b45f2b;
    }
    
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
    }
    .menu-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        transition: 0.3s;
    }
    .menu-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
    .menu-image {
        height: 160px;
        background: #f5f0eb;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .menu-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .menu-image .placeholder {
        font-size: 40px;
        color: #ccc;
    }
    .menu-card .info {
        padding: 14px 16px;
    }
    .menu-card .info h4 {
        font-size: 1rem;
        margin-bottom: 4px;
        color: #2c1f16;
    }
    .menu-card .info .desc {
        font-size: 0.8rem;
        color: #777;
        margin-bottom: 6px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 35px;
    }
    .menu-card .info .price {
        font-weight: 700;
        color: #b45f2b;
        font-size: 1.1rem;
        margin-bottom: 8px;
    }
    .menu-card .info .btn {
        font-size: 0.8rem;
        padding: 6px 18px;
        width: 100%;
        text-align: center;
    }
    .view-menu-btn {
        text-align: center;
        margin-top: 25px;
    }
    
    /* Services - Minimized */
    .services-section {
        padding: 30px 0 40px 0;
        background: #f7f2e9;
    }
    .services-section .section-title {
        margin-top: 0;
        margin-bottom: 25px;
        text-align: center;
        font-size: 2rem;
        color: #2c1f16;
        position: relative;
        padding-bottom: 15px;
    }
    .services-section .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: #b45f2b;
    }
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 20px;
    }
    .service-item {
        background: #fff;
        padding: 25px 15px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        transition: 0.3s;
        border: 1px solid #eee;
    }
    .service-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }
    .service-item i {
        font-size: 2.2rem;
        color: #b45f2b;
        margin-bottom: 10px;
    }
    .service-item h4 {
        font-size: 1rem;
        color: #2c1f16;
        margin-bottom: 4px;
    }
    .service-item p {
        color: #666;
        font-size: 0.8rem;
        margin: 0;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .hero {
            min-height: 50vh;
            padding: 30px 15px;
        }
        .hero h1 {
            font-size: 2rem;
        }
        .hero .subtitle {
            font-size: 0.9rem;
        }
        .btn-group {
            flex-direction: column;
            align-items: center;
        }
        .btn-group .btn {
            width: 100%;
            max-width: 250px;
        }
        .menu-grid {
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        }
        .services-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 480px) {
        .hero h1 {
            font-size: 1.6rem;
        }
        .menu-grid {
            grid-template-columns: 1fr 1fr;
        }
        .services-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>

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
<section class="menu-preview">
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
                    <a href="order.php?add_to_cart=<?php echo $row['id']; ?>" class="btn" style="font-size:0.8rem;padding:6px 18px;">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="view-menu-btn">
            <a href="menu.php" class="btn">View Full Menu</a>
        </div>
    </div>
</section>

<!-- SERVICES -->
<section class="services-section">
    <div class="container">
        <h2 class="section-title">Our Services</h2>
        <div class="services-grid">
            <div class="service-item">
                <i class="fas fa-utensils"></i>
                <h4>Dine-in</h4>
                <p>Enjoy at our restaurant</p>
            </div>
            <div class="service-item">
                <i class="fas fa-truck"></i>
                <h4>Delivery</h4>
                <p>Food to your door</p>
            </div>
            <div class="service-item">
                <i class="fas fa-shopping-bag"></i>
                <h4>Takeaway</h4>
                <p>Order and pick up</p>
            </div>
            <div class="service-item">
                <i class="fas fa-calendar-check"></i>
                <h4>Reservation</h4>
                <p>Book your table</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
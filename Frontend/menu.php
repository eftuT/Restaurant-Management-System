<?php
// No session_start() here - it's in header.php
require_once 'includes/header.php';
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

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

$categoryIcons = [
    'breakfast' => 'fa-sun',
    'lunch' => 'fa-utensils',
    'dinner' => 'fa-moon',
    'beverage' => 'fa-mug-hot',
    'snack' => 'fa-bolt',
    'dessert' => 'fa-cake'
];
$categoryNames = [
    'breakfast' => 'Breakfast',
    'lunch' => 'Lunch',
    'dinner' => 'Dinner',
    'beverage' => 'Beverages',
    'snack' => 'Snacks',
    'dessert' => 'Desserts'
];
$order = ['breakfast', 'lunch', 'dinner', 'beverage', 'snack', 'dessert'];
?>

<style>
    .menu-page { 
        padding: 20px 0 40px 0;
    }
    .menu-page .section-title {
        margin-top: 0;
        margin-bottom: 5px;
        text-align: center;
        font-size: 2.5rem;
        color: #2c1f16;
        position: relative;
        padding-bottom: 15px;
    }
    .menu-page .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: #b45f2b;
    }
    .menu-page .subtitle { 
        text-align: center; 
        color: #666; 
        margin-bottom: 30px;
        font-size: 1rem;
    }
    .category-section { 
        margin-bottom: 35px;
    }
    .category-title { 
        font-size: 1.6rem;
        color: #2c1f16; 
        padding-bottom: 8px; 
        border-bottom: 3px solid #b45f2b; 
        margin-bottom: 20px;
        text-transform: capitalize; 
        display: flex; 
        align-items: center; 
        gap: 10px;
    }
    .category-title i { color: #b45f2b; font-size: 1.5rem; }
    .no-items { text-align: center; padding: 30px; color: #666; }
    .no-items i { font-size: 40px; color: #ddd; display: block; margin-bottom: 15px; }
    
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
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
</style>

<section class="menu-page">
    <div class="container">
        <h2 class="section-title">Our Menu</h2>
        <p class="subtitle">Authentic Ethiopian flavors made with love</p>

        <?php if(empty($categories)): ?>
            <div class="no-items">
                <i class="fas fa-utensils"></i>
                <h3>No menu items available</h3>
                <p>Please check back later or contact us for more information.</p>
            </div>
        <?php else: ?>
            <?php foreach($order as $category): 
                if(isset($categories[$category]) && !empty($categories[$category])):
            ?>
            <div class="category-section">
                <h3 class="category-title">
                    <i class="fas <?php echo $categoryIcons[$category] ?? 'fa-utensils'; ?>"></i>
                    <?php echo $categoryNames[$category] ?? ucfirst($category); ?>
                </h3>
                <div class="menu-grid">
                    <?php foreach($categories[$category] as $item): 
                        $imageSrc = '';
                        if(!empty($item['image_url'])) {
                            $imagePath = __DIR__ . '/../' . $item['image_url'];
                            if(file_exists($imagePath)) {
                                $imageSrc = '/Restaurant-Management-System/' . $item['image_url'];
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
                            <a href="cart.php?add=<?php echo $item['id']; ?>" class="btn" style="font-size:0.8rem;padding:6px 18px;">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </a>
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

<?php require_once 'includes/footer.php'; ?>
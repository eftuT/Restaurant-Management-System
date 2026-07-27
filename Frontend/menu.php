<?php
require_once 'includes/header.php';
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

// Check if image_url column exists
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

// Define category icons and names
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
    .category-title i { color: #b45f2b; font-size: 1.8rem; }
    .no-items { text-align: center; padding: 40px; color: #666; }
    .no-items i { font-size: 50px; color: #ddd; display: block; margin-bottom: 20px; }
</style>

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

<?php require_once 'includes/footer.php'; ?>
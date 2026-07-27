<?php
// ============================================================
// NO session_start() HERE - IT'S ALREADY IN HEADER.PHP
// ============================================================

require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

// ===== ADD TO CART - MUST BE BEFORE HEADER =====
if (isset($_GET['add_to_cart']) && is_numeric($_GET['add_to_cart'])) {
    $item_id = (int)$_GET['add_to_cart'];
    $quantity = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $stmt = $conn->prepare("SELECT * FROM food WHERE id = ? AND is_available = 1");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        
        $found = false;
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['id'] == $item_id) {
                $cart_item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $item['id'],
                'name' => $item['food_name'],
                'price' => $item['food_price'],
                'quantity' => $quantity,
                'image' => $item['image_url'] ?? ''
            ];
        }
    }
    
    header('Location: cart.php');
    exit;
}
// ===== END ADD TO CART =====

require_once 'includes/header.php';

$error = '';
$success = '';

// Category configuration
$categoryOrder = ['breakfast', 'lunch', 'dinner', 'beverage', 'snack', 'dessert'];

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

// Get all food items ordered by category
$menuItems = $conn->query("SELECT * FROM food WHERE is_available = 1 ORDER BY 
    FIELD(food_category, 'breakfast', 'lunch', 'dinner', 'beverage', 'snack', 'dessert'), 
    food_name ASC");

$categories = [];
while($row = $menuItems->fetch_assoc()) {
    $categories[$row['food_category']][] = $row;
}

$totalItems = 0;
foreach($categories as $items) {
    $totalItems += count($items);
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $order_type = $_POST['order_type'] ?? 'dine_in';
    $items = $_POST['items'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    
    $order_items = [];
    $total = 0;
    foreach($items as $index => $item_id) {
        if(isset($quantities[$index]) && $quantities[$index] > 0) {
            $stmt = $conn->prepare("SELECT * FROM food WHERE id = ?");
            $stmt->bind_param("i", $item_id);
            $stmt->execute();
            $food = $stmt->get_result()->fetch_assoc();
            if($food) {
                $qty = (int)$quantities[$index];
                $order_items[] = [
                    'id' => $food['id'],
                    'name' => $food['food_name'],
                    'price' => $food['food_price'],
                    'quantity' => $qty
                ];
                $total += $food['food_price'] * $qty;
            }
        }
    }
    
    if(empty($order_items)) {
        $error = 'Please select at least one item';
    } elseif(empty($full_name) || empty($phone)) {
        $error = 'Name and phone are required';
    } else {
        $conn->query("CREATE TABLE IF NOT EXISTS basket (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT,
            customer_name VARCHAR(100) NOT NULL,
            address TEXT,
            email VARCHAR(100),
            contact_number VARCHAR(20),
            total DECIMAL(10,2) NOT NULL,
            status VARCHAR(20) DEFAULT 'pending',
            items TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $conn->query("CREATE TABLE IF NOT EXISTS items (
            id INT PRIMARY KEY AUTO_INCREMENT,
            order_id INT NOT NULL,
            food VARCHAR(100) NOT NULL,
            qty INT NOT NULL
        )");
        
        $items_json = json_encode($order_items);
        $stmt = $conn->prepare("INSERT INTO basket (customer_name, address, email, contact_number, total, status, items) VALUES (?, ?, ?, ?, ?, 'pending', ?)");
        $stmt->bind_param("ssssds", $full_name, $address, $email, $phone, $total, $items_json);
        
        if($stmt->execute()) {
            $order_id = $conn->insert_id;
            foreach($order_items as $item) {
                $stmt2 = $conn->prepare("INSERT INTO items (order_id, food, qty) VALUES (?, ?, ?)");
                $stmt2->bind_param("isi", $order_id, $item['name'], $item['quantity']);
                $stmt2->execute();
            }
            $_SESSION['cart'] = [];
            $success = 'Order placed successfully! Total: ' . $total . ' Br';
        } else {
            $error = 'Order failed. Please try again.';
        }
    }
}
?>

<style>
    .order-page { padding: 40px 0; }
    
    /* ===== TITLE WITH BROWN LINE ===== */
    .order-page h2 { 
        text-align: center; 
        font-size: 2.4rem; 
        color: #2c1f16; 
        margin-bottom: 10px; 
        position: relative;
    }
    .order-page h2:after {
        content: '';
        display: block;
        width: 80px;
        height: 4px;
        background: #b45f2b;
        margin: 10px auto 0;
    }
    .order-page .subtitle { 
        text-align: center; 
        color: #666; 
        margin-bottom: 40px; 
        font-size: 1rem;
    }
    
    .order-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
    
    /* ===== ALERTS ===== */
    .alert {
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        position: relative;
        animation: slideDown 0.4s ease;
    }
    @keyframes slideDown {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .alert-success {
        background: #edf7ed;
        color: #1e8449;
        border-left: 4px solid #1e8449;
    }
    .alert-error {
        background: #fdf0ed;
        color: #c0392b;
        border-left: 4px solid #c0392b;
    }
    .alert i { font-size: 1.2rem; }
    .alert-close {
        margin-left: auto;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: inherit;
        opacity: 0.6;
        padding: 0 4px;
        transition: 0.3s;
        line-height: 1;
    }
    .alert-close:hover { opacity: 1; }
    
    /* ===== ORDER FORM ===== */
    .order-form { background: #fff; padding: 30px; border-radius: 20px; box-shadow: 0 6px 18px rgba(0,0,0,0.08); }
    
    /* ===== CATEGORY ACCORDION ===== */
    .category-accordion { margin-bottom: 10px; border-radius: 10px; overflow: hidden; border: 1px solid #f0ebe3; }
    .category-header {
        background: #faf8f5;
        padding: 12px 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: 0.3s;
        user-select: none;
        border-left: 4px solid #b45f2b;
    }
    .category-header:hover { background: #f5f0ea; }
    .category-header .left { display: flex; align-items: center; gap: 10px; }
    .category-header .left i { font-size: 1rem; color: #b45f2b; width: 20px; text-align: center; }
    .category-header .left .name { font-weight: 600; color: #2c1f16; font-size: 0.95rem; }
    .category-header .left .count { 
        background: #b45f2b; 
        color: #fff; 
        padding: 0 8px; 
        border-radius: 20px; 
        font-size: 10px; 
        font-weight: 700; 
        line-height: 18px;
    }
    .category-header .arrow { transition: 0.3s; color: #999; font-size: 0.8rem; }
    .category-header .arrow.active { transform: rotate(180deg); }
    
    .category-body { 
        max-height: 0; 
        overflow: hidden; 
        transition: max-height 0.4s ease;
        background: #fff;
    }
    .category-body.open { max-height: 2000px; }
    .category-body .inner { padding: 0 12px 12px; }
    
    .category-body .menu-item-select { 
        display: flex; 
        align-items: center; 
        gap: 10px; 
        padding: 8px 10px; 
        border-bottom: 1px solid #f5f0ea; 
        transition: 0.2s;
        border-radius: 6px;
    }
    .category-body .menu-item-select:last-child { border-bottom: none; }
    .category-body .menu-item-select:hover { background: #faf8f5; }
    .category-body .menu-item-select input[type="checkbox"] { 
        width: 16px; 
        height: 16px; 
        accent-color: #b45f2b;
        cursor: pointer;
        flex-shrink: 0;
    }
    .category-body .menu-item-select .item-info { flex: 1; }
    .category-body .menu-item-select .item-info strong { 
        font-size: 0.9rem; 
        color: #2c1f16; 
    }
    .category-body .menu-item-select .item-info .desc { 
        font-size: 0.75rem; 
        color: #888; 
        display: block; 
    }
    .category-body .menu-item-select .item-price { 
        font-weight: 700; 
        color: #b45f2b; 
        font-size: 0.9rem;
        min-width: 50px;
        text-align: right;
    }
    .category-body .menu-item-select .qty-input { 
        width: 48px; 
        padding: 4px 6px; 
        border: 2px solid #e0e0e0; 
        border-radius: 6px; 
        text-align: center; 
        font-size: 0.85rem;
        transition: 0.3s;
        background: #faf8f5;
    }
    .category-body .menu-item-select .qty-input:focus { 
        border-color: #b45f2b; 
        outline: none; 
        box-shadow: 0 0 0 3px rgba(180,95,43,0.08);
    }
    .category-body .menu-item-select .qty-input:disabled { 
        background: #f5f5f5; 
        cursor: not-allowed; 
        opacity: 0.6;
    }
    
    .order-form hr { 
        margin: 20px 0; 
        border: none; 
        border-top: 2px solid #f0ebe3; 
    }
    
    .order-form .section-title-sm { 
        font-size: 1rem; 
        font-weight: 700; 
        color: #2c1f16; 
        margin-bottom: 12px; 
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .order-form .section-title-sm .total-badge {
        background: #b45f2b;
        color: #fff;
        padding: 0 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        line-height: 22px;
    }
    
    /* ===== 2-COLUMN DELIVERY FORM ===== */
    .form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
    .form-row-2 .full-width {
        grid-column: 1 / -1;
    }
    .order-form label { 
        display: block; 
        font-weight: 600; 
        margin-top: 10px; 
        color: #2c1f16; 
        font-size: 0.85rem;
    }
    .order-form label .required { color: #e74c3c; }
    .order-form input, .order-form select, .order-form textarea { 
        width: 100%; 
        padding: 10px 14px; 
        border: 2px solid #e0e0e0; 
        border-radius: 10px; 
        margin-top: 3px; 
        font-size: 14px; 
        transition: 0.3s; 
        background: #faf8f5;
    }
    .order-form input:focus, .order-form select:focus, .order-form textarea:focus { 
        border-color: #b45f2b; 
        outline: none; 
        box-shadow: 0 0 0 3px rgba(180,95,43,0.08);
        background: #fff;
    }
    .order-form .btn { 
        width: 100%; 
        margin-top: 18px; 
        padding: 14px; 
        border: none; 
        border-radius: 12px; 
        background: linear-gradient(135deg, #b45f2b, #8a471f); 
        color: #fff; 
        font-weight: 600; 
        font-size: 15px; 
        cursor: pointer; 
        transition: 0.3s; 
    }
    .order-form .btn:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 25px rgba(180,95,43,0.25);
    }
    .order-form .btn i { margin-right: 8px; }
    
    /* ===== ORDER SUMMARY - COMPACT ===== */
    .order-summary { 
        background: #f7f2e9; 
        padding: 18px 20px; 
        border-radius: 16px; 
        position: sticky; 
        top: 20px; 
        border: 1px solid #f0ebe3;
        min-height: 100px;
        max-height: 400px;
        display: flex;
        flex-direction: column;
    }
    .order-summary h3 { 
        color: #2c1f16; 
        font-size: 1rem; 
        margin-bottom: 8px; 
        padding-bottom: 8px;
        border-bottom: 2px solid #e8e0d8;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }
    .order-summary h3 .badge-cart {
        background: #b45f2b;
        color: #fff;
        padding: 0 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        line-height: 20px;
    }
    .order-summary .items-list { 
        flex: 1;
        overflow-y: auto;
        padding-right: 4px;
    }
    .order-summary .items-list::-webkit-scrollbar { width: 3px; }
    .order-summary .items-list::-webkit-scrollbar-thumb { background: #ddd; border-radius: 4px; }
    .order-summary .item { 
        display: flex; 
        justify-content: space-between; 
        padding: 3px 0; 
        border-bottom: 1px solid #e8e0d8; 
        font-size: 0.82rem;
    }
    .order-summary .item:last-child { border-bottom: none; }
    .order-summary .item .qty-badge { 
        background: #b45f2b; 
        color: #fff; 
        padding: 0 6px; 
        border-radius: 10px; 
        font-size: 9px; 
        font-weight: 700;
        display: inline-block;
        margin-right: 4px;
    }
    .order-summary .item .item-name { flex: 1; font-size: 0.82rem; }
    .order-summary .item .item-total { font-weight: 600; color: #2c1f16; font-size: 0.82rem; }
    .order-summary .total { 
        font-size: 1.1rem; 
        font-weight: 700; 
        color: #b45f2b; 
        margin-top: 8px; 
        padding-top: 8px;
        border-top: 2px solid #e8e0d8;
        text-align: right;
        flex-shrink: 0;
    }
    .order-summary .empty-summary { 
        color: #999; 
        text-align: center; 
        padding: 15px 0; 
        font-size: 0.85rem; 
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
    }
    .order-summary .empty-summary i { font-size: 1.8rem; display: block; margin-bottom: 4px; color: #ddd; }
    
    @media (max-width: 800px) { 
        .order-grid { grid-template-columns: 1fr; }
        .order-summary { position: static; margin-top: 20px; max-height: 300px; }
        .form-row-2 { grid-template-columns: 1fr; gap: 0; }
        .category-header { padding: 10px 14px; }
        .category-header .left { font-size: 0.85rem; }
        .category-body .menu-item-select { flex-wrap: wrap; padding: 6px 8px; }
        .category-body .menu-item-select .item-info { flex: 1 1 100%; order: 2; }
        .category-body .menu-item-select input[type="checkbox"] { order: 1; }
        .category-body .menu-item-select .item-price { order: 3; }
        .category-body .menu-item-select .qty-input { order: 4; }
    }
</style>

<section class="order-page">
    <div class="container">
        <h2>Place Your Order</h2>
        <p class="subtitle">Select your favorite Ethiopian dishes and customize your order</p>
        
        <!-- ===== ALERTS (PERSISTENT) ===== -->
        <?php if($success): ?>
            <div class="alert alert-success" id="successAlert">
                <i class="fas fa-check-circle"></i> 
                <?php echo $success; ?>
                <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-error" id="errorAlert">
                <i class="fas fa-exclamation-circle"></i> 
                <?php echo $error; ?>
                <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>

        <div class="order-grid">
            <div class="order-form">
                <form method="POST" action="" id="orderForm">
                    <div class="section-title-sm">
                        <i class="fas fa-utensils"></i> Select Items
                        <span class="total-badge"><?php echo $totalItems; ?> items</span>
                    </div>
                    
                    <?php if(empty($categories)): ?>
                        <div style="text-align:center;padding:30px 0;color:#999;">
                            <i class="fas fa-utensils" style="font-size:2.5rem;display:block;margin-bottom:10px;color:#ddd;"></i>
                            <p>No menu items available. Please check back later.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($categoryOrder as $category): 
                            if(isset($categories[$category]) && !empty($categories[$category])):
                        ?>
                        <div class="category-accordion">
                            <div class="category-header" onclick="toggleCategory(this)">
                                <div class="left">
                                    <i class="fas <?php echo $categoryIcons[$category] ?? 'fa-utensils'; ?>"></i>
                                    <span class="name"><?php echo $categoryNames[$category] ?? ucfirst($category); ?></span>
                                    <span class="count"><?php echo count($categories[$category]); ?></span>
                                </div>
                                <div class="arrow"><i class="fas fa-chevron-down"></i></div>
                            </div>
                            <div class="category-body">
                                <div class="inner">
                                    <?php foreach($categories[$category] as $item): ?>
                                    <div class="menu-item-select">
                                        <input type="checkbox" name="items[]" value="<?php echo $item['id']; ?>" onchange="updateTotal()">
                                        <div class="item-info">
                                            <strong><?php echo htmlspecialchars($item['food_name']); ?></strong>
                                            <span class="desc"><?php echo htmlspecialchars($item['food_description'] ?? ''); ?></span>
                                        </div>
                                        <div class="item-price"><?php echo $item['food_price']; ?> Br</div>
                                        <input type="number" name="quantities[]" class="qty-input" value="1" min="1" max="10" onchange="updateTotal()" disabled>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php 
                            endif; 
                        endforeach; 
                        ?>
                    <?php endif; ?>

                    <hr>

                    <div class="section-title-sm"><i class="fas fa-user"></i> Delivery Information</div>
                    
                    <div class="form-row-2">
                        <div>
                            <label>Full Name <span class="required">*</span></label>
                            <input type="text" name="full_name" placeholder="Enter your full name" required value="<?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ''; ?>">
                        </div>
                        <div>
                            <label>Phone Number <span class="required">*</span></label>
                            <input type="tel" name="phone" placeholder="Enter your phone number" required>
                        </div>
                    </div>
                    
                    <div class="form-row-2">
                        <div>
                            <label>Email</label>
                            <input type="email" name="email" placeholder="Enter your email" value="<?php echo isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''; ?>">
                        </div>
                        <div>
                            <label>Order Type</label>
                            <select name="order_type">
                                <option value="dine_in">Dine-in</option>
                                <option value="takeaway">Takeaway</option>
                                <option value="delivery">Delivery</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row-2">
                        <div class="full-width">
                            <label>Delivery Address</label>
                            <input type="text" name="address" placeholder="Enter your delivery address">
                        </div>
                    </div>

                    <button type="submit" class="btn"><i class="fas fa-shopping-cart"></i> Place Order</button>
                </form>
            </div>

            <div class="order-summary">
                <h3>
                    <i class="fas fa-receipt"></i> Summary
                    <span class="badge-cart" id="itemCountBadge">0</span>
                </h3>
                <div class="items-list" id="selectedItems">
                    <div class="empty-summary">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Select items</p>
                    </div>
                </div>
                <div class="total">Total: <span id="totalAmount">0</span> Br</div>
            </div>
        </div>
    </div>
</section>

<script>
function toggleCategory(header) {
    const body = header.nextElementSibling;
    const arrow = header.querySelector('.arrow');
    body.classList.toggle('open');
    arrow.classList.toggle('active');
}

function updateTotal() {
    const items = document.querySelectorAll('.menu-item-select');
    let total = 0;
    let html = '';
    let itemCount = 0;
    
    items.forEach(item => {
        const checkbox = item.querySelector('input[type="checkbox"]');
        const qtyInput = item.querySelector('.qty-input');
        const qty = parseInt(qtyInput.value) || 0;
        const price = parseFloat(item.querySelector('.item-price').textContent);
        const name = item.querySelector('.item-info strong').textContent;
        
        qtyInput.disabled = !checkbox.checked;
        
        if(checkbox.checked && qty > 0) {
            const subtotal = price * qty;
            total += subtotal;
            itemCount += qty;
            html += `<div class="item">
                <span class="item-name">
                    <span class="qty-badge">${qty}</span>
                    ${name}
                </span>
                <span class="item-total">${subtotal} Br</span>
            </div>`;
        }
    });
    
    if (itemCount === 0) {
        document.getElementById('selectedItems').innerHTML = `
            <div class="empty-summary">
                <i class="fas fa-shopping-cart"></i>
                <p>Select items</p>
            </div>
        `;
    } else {
        document.getElementById('selectedItems').innerHTML = html;
    }
    
    document.getElementById('totalAmount').textContent = total;
    document.getElementById('itemCountBadge').textContent = itemCount;
}

document.querySelectorAll('.menu-item-select input[type="checkbox"]').forEach(el => {
    el.addEventListener('change', function() {
        const qtyInput = this.closest('.menu-item-select').querySelector('.qty-input');
        qtyInput.disabled = !this.checked;
        if (!this.checked) {
            qtyInput.value = 1;
        }
        updateTotal();
    });
});

document.querySelectorAll('.qty-input').forEach(el => {
    el.addEventListener('change', updateTotal);
    el.addEventListener('input', updateTotal);
});

document.addEventListener('DOMContentLoaded', function() {
    const firstHeader = document.querySelector('.category-header');
    if (firstHeader) {
        firstHeader.click();
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
<?php

require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';


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

require_once 'includes/header.php';

$error = '';
$success = '';
$menu_items = $conn->query("SELECT * FROM food WHERE is_available = 1");

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
    .order-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
    .order-form { background: #fff; padding: 30px; border-radius: 20px; box-shadow: 0 6px 18px rgba(0,0,0,0.08); }
    .order-form label { display: block; font-weight: 600; margin-top: 12px; }
    .order-form input, .order-form select, .order-form textarea { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; margin-top: 4px; font-size: 15px; transition: 0.3s; }
    .order-form input:focus, .order-form select:focus, .order-form textarea:focus { border-color: #b45f2b; outline: none; box-shadow: 0 0 0 3px rgba(180,95,43,0.1); }
    .order-form .btn { width: 100%; margin-top: 20px; padding: 14px; }
    .order-form .error { background: #fee; color: #c00; padding: 12px; border-radius: 10px; margin-bottom: 15px; text-align: center; border-left: 4px solid #c00; }
    .order-form .success { background: #efe; color: #060; padding: 12px; border-radius: 10px; margin-bottom: 15px; text-align: center; border-left: 4px solid #060; }
    .order-summary { background: #f7f2e9; padding: 30px; border-radius: 20px; position: sticky; top: 20px; }
    .order-summary h3 { color: #2c1f16; margin-bottom: 20px; }
    .order-summary .item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #ddd; }
    .order-summary .total { font-size: 1.5rem; font-weight: 700; color: #b45f2b; margin-top: 20px; text-align: right; }
    .menu-item-select { display: flex; align-items: center; gap: 15px; padding: 10px 0; border-bottom: 1px solid #eee; }
    .menu-item-select input[type="checkbox"] { width: 20px; height: 20px; }
    .menu-item-select .qty-input { width: 60px; padding: 5px; border: 2px solid #e0e0e0; border-radius: 8px; text-align: center; }
    .menu-item-select .item-info { flex: 1; }
    .menu-item-select .item-price { font-weight: 600; color: #b45f2b; }
    @media (max-width: 800px) { .order-grid { grid-template-columns: 1fr; } }
</style>

<section class="order-page">
    <div class="container">
        <h2 class="section-title">Place Your Order</h2>
        
        <?php if($error): ?>
            <div class="error" style="background:#fee;color:#c00;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center;border-left:4px solid #c00;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="success" style="background:#efe;color:#060;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center;border-left:4px solid #060;">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="order-grid">
            <div class="order-form">
                <h3>Select Items</h3>
                <form method="POST" action="" id="orderForm">
                    <?php 
                    $menu_items = $conn->query("SELECT * FROM food WHERE is_available = 1");
                    while($item = $menu_items->fetch_assoc()): 
                    ?>
                    <div class="menu-item-select">
                        <input type="checkbox" name="items[]" value="<?php echo $item['id']; ?>" onchange="updateTotal()">
                        <div class="item-info">
                            <strong><?php echo $item['food_name']; ?></strong>
                            <div style="font-size:0.85rem;color:#666;"><?php echo $item['food_description']; ?></div>
                        </div>
                        <div class="item-price"><?php echo $item['food_price']; ?> Br</div>
                        <input type="number" name="quantities[]" class="qty-input" value="1" min="1" max="10" onchange="updateTotal()">
                    </div>
                    <?php endwhile; ?>

                    <hr style="margin:20px 0;">

                    <h3>Delivery Information</h3>
                    
                    <label>Full Name *</label>
                    <input type="text" name="full_name" placeholder="Enter your name" required value="<?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ''; ?>">

                    <label>Email</label>
                    <input type="email" name="email" placeholder="Enter your email" value="<?php echo isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''; ?>">

                    <label>Phone Number *</label>
                    <input type="tel" name="phone" placeholder="Enter phone number" required>

                    <label>Address</label>
                    <input type="text" name="address" placeholder="Enter delivery address">

                    <label>Order Type</label>
                    <select name="order_type">
                        <option value="dine_in">Dine-in</option>
                        <option value="takeaway">Takeaway</option>
                        <option value="delivery">Delivery</option>
                    </select>

                    <button type="submit" class="btn"><i class="fas fa-shopping-cart"></i> Place Order</button>
                </form>
            </div>

            <div class="order-summary">
                <h3>Order Summary</h3>
                <div id="selectedItems">
                    <p style="color:#999;">Select items to see summary</p>
                </div>
                <div class="total">Total: <span id="totalAmount">0</span> Br</div>
            </div>
        </div>
    </div>
</section>

<script>
function updateTotal() {
    const items = document.querySelectorAll('.menu-item-select');
    let total = 0;
    let html = '';
    
    items.forEach(item => {
        const checkbox = item.querySelector('input[type="checkbox"]');
        const qty = parseInt(item.querySelector('.qty-input').value) || 0;
        const price = parseFloat(item.querySelector('.item-price').textContent);
        const name = item.querySelector('.item-info strong').textContent;
        
        if(checkbox.checked && qty > 0) {
            const subtotal = price * qty;
            total += subtotal;
            html += `<div class="item"><span>${name} x${qty}</span><span>${subtotal} Br</span></div>`;
        }
    });
    
    document.getElementById('selectedItems').innerHTML = html || '<p style="color:#999;">Select items to see summary</p>';
    document.getElementById('totalAmount').textContent = total;
}

document.querySelectorAll('.menu-item-select input').forEach(el => {
    el.addEventListener('change', updateTotal);
});
</script>

<?php require_once 'includes/footer.php'; ?>
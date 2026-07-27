<?php
// ============================================================
// ALL REDIRECT LOGIC MUST GO HERE - BEFORE ANY OUTPUT
// ============================================================

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

// ===== ADD TO CART =====
if (isset($_GET['add']) && is_numeric($_GET['add'])) {
    $item_id = (int)$_GET['add'];
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

// ===== UPDATE QUANTITY =====
if (isset($_GET['update']) && is_numeric($_GET['update'])) {
    $item_id = (int)$_GET['update'];
    $quantity = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if ($quantity <= 0) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $item_id) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    } else {
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $item_id) {
                $item['quantity'] = $quantity;
                break;
            }
        }
    }
    header('Location: cart.php');
    exit;
}

// ===== REMOVE ITEM =====
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $remove_id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header('Location: cart.php');
    exit;
}

// ===== CLEAR CART =====
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
    header('Location: cart.php');
    exit;
}
// ===== END CART LOGIC =====

// ============================================================
// NOW INCLUDE HEADER - AFTER ALL REDIRECTS
// ============================================================
require_once 'includes/header.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<style>
    .cart-page { padding: 40px 0; }
    .cart-page h2 { 
        text-align: center; 
        font-size: 2.4rem; 
        color: #2c1f16; 
        margin-bottom: 10px; 
        position: relative;
    }
    .cart-page h2:after {
        content: '';
        display: block;
        width: 80px;
        height: 4px;
        background: #b45f2b;
        margin: 10px auto 0;
    }
    .cart-page .subtitle { 
        text-align: center; 
        color: #666; 
        margin-bottom: 40px; 
        font-size: 1rem;
    }
    .cart-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); }
    .cart-table th { background: #f8f9fa; padding: 15px; text-align: left; font-weight: 600; }
    .cart-table td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
    .cart-table .qty-input { width: 60px; padding: 8px; border: 2px solid #e0e0e0; border-radius: 8px; text-align: center; }
    .cart-table .qty-input:focus { border-color: #b45f2b; outline: none; }
    .cart-summary { background: #f7f2e9; padding: 30px; border-radius: 20px; margin-top: 30px; text-align: right; }
    .cart-summary .total { font-size: 2rem; font-weight: 700; color: #b45f2b; }
    .cart-summary .subtotal { font-size: 1.2rem; color: #666; }
    .empty-cart { text-align: center; padding: 60px 20px; }
    .empty-cart i { font-size: 4rem; color: #ddd; display: block; margin-bottom: 20px; }
    .empty-cart h3 { color: #2c1f16; margin-bottom: 10px; }
    .empty-cart p { color: #999; }
    .cart-actions { display: flex; gap: 15px; margin-top: 30px; flex-wrap: wrap; justify-content: flex-end; }
    .btn-danger { background: #e74c3c; color: #fff; }
    .btn-danger:hover { background: #c0392b; }
    @media (max-width: 800px) { .cart-table { font-size: 0.85rem; } .cart-table th, .cart-table td { padding: 10px; } }
</style>

<section class="cart-page">
    <div class="container">
        <h2>Your Cart</h2>
        <p class="subtitle">Review your items before checkout</p>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty</h3>
                <p>Browse our menu and add items you love!</p>
                <a href="menu.php" class="btn" style="margin-top:20px;">Browse Menu</a>
            </div>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                        <td><?php echo $item['price']; ?> Br</td>
                        <td>
                            <input type="number" class="qty-input" value="<?php echo $item['quantity']; ?>" min="0" max="10" onchange="updateQty(<?php echo $item['id']; ?>, this.value)">
                        </td>
                        <td><strong><?php echo $item['price'] * $item['quantity']; ?> Br</strong></td>
                        <td>
                            <a href="cart.php?remove=<?php echo $item['id']; ?>" class="btn btn-danger" style="padding:5px 15px;font-size:0.8rem;" onclick="return confirm('Remove this item?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <div class="subtotal">Subtotal</div>
                <div class="total"><?php echo $total; ?> Br</div>
                <div style="margin-top:10px;font-size:0.9rem;color:#999;">
                    <i class="fas fa-info-circle"></i> Tax and delivery fees calculated at checkout
                </div>
                <div class="cart-actions">
                    <a href="cart.php?clear=1" class="btn btn-outline" onclick="return confirm('Clear your cart?')">
                        <i class="fas fa-trash"></i> Clear Cart
                    </a>
                    <a href="menu.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Continue Ordering
                    </a>
                    <a href="order.php" class="btn">
                        <i class="fas fa-shopping-cart"></i> Proceed to Checkout
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function updateQty(itemId, quantity) {
    if (quantity < 0) quantity = 0;
    window.location.href = 'cart.php?update=' + itemId + '&qty=' + quantity;
}
</script>

<?php require_once 'includes/footer.php'; ?>
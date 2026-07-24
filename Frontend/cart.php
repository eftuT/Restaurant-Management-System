<?php
session_start();
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add item to cart
if (isset($_GET['add']) && is_numeric($_GET['add'])) {
    $item_id = (int)$_GET['add'];
    $quantity = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
    
    // Check if item exists in database
    $stmt = $conn->prepare("SELECT * FROM food WHERE id = ? AND is_available = 1");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        
        // Check if item already in cart
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

// Remove item from cart
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
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

// Update quantity
if (isset($_POST['update'])) {
    $item_id = (int)$_POST['item_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($quantity <= 0) {
        // Remove item if quantity is 0
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

// Clear cart
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
    header('Location: cart.php');
    exit;
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - SEN'Q Restaurant</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;400;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4a085e9b53.js" crossorigin="anonymous"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #fefcf3; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .btn { display: inline-block; background: #b45f2b; color: #fff; padding: 12px 30px; border-radius: 40px; font-weight: 600; border: none; cursor: pointer; transition: 0.3s; font-size: 1rem; text-decoration: none; }
        .btn:hover { background: #8a471f; transform: scale(1.03); }
        .btn-outline { background: transparent; border: 2px solid #b45f2b; color: #b45f2b; }
        .btn-outline:hover { background: #b45f2b; color: #fff; }
        .btn-danger { background: #e74c3c; color: #fff; }
        .btn-danger:hover { background: #c0392b; }
        .btn-success { background: #27ae60; color: #fff; }
        .btn-success:hover { background: #1e8449; }
        
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
        
        .cart-page { padding: 40px 0; }
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
        .cart-actions { display: flex; gap: 15px; margin-top: 30px; flex-wrap: wrap; }
        
        @media (max-width: 800px) { .cart-table { font-size: 0.85rem; } .cart-table th, .cart-table td { padding: 10px; } }
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
                <a href="contact.php">Contact</a>
                <a href="about.php">About</a>
            </nav>
        </div>
    </div>
</header>

<section class="cart-page">
    <div class="container">
        <h2 class="section-title">Your Cart</h2>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty</h3>
                <p>Browse our menu and add items you love!</p>
                <a href="menu.php" class="btn" style="margin-top:20px;">Browse Menu</a>
            </div>
        <?php else: ?>
            <form method="POST" action="">
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
                            <td>
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                            </td>
                            <td><?php echo $item['price']; ?> Br</td>
                            <td>
                                <input type="number" name="quantity" class="qty-input" value="<?php echo $item['quantity']; ?>" min="0" max="10" onchange="updateQty(<?php echo $item['id']; ?>, this.value)">
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
            </form>

            <div class="cart-summary">
                <div class="subtotal">Subtotal</div>
                <div class="total"><?php echo $total; ?> Br</div>
                <div style="margin-top:10px;font-size:0.9rem;color:#999;">
                    <i class="fas fa-info-circle"></i> Tax and delivery fees calculated at checkout
                </div>
                <div class="cart-actions" style="justify-content:flex-end;">
                    <a href="cart.php?clear=1" class="btn btn-outline" onclick="return confirm('Clear your cart?')">
                        <i class="fas fa-trash"></i> Clear Cart
                    </a>
                    <a href="menu.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                    <a href="order.php" class="btn btn-success">
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
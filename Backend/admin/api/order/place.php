<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
require_once '../../includes/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

$order_type = $data['order_type'] ?? 'dine_in';
$items = $data['items'] ?? [];
$total = $data['total'] ?? 0;
$address = $data['address'] ?? '';

if (empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

$customer_name = $_SESSION['user_name'] ?? 'Guest';
$email = $_SESSION['user_email'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;

$items_json = json_encode($items);

$query = "INSERT INTO basket (user_id, customer_name, email, address, total, status, items) 
          VALUES ('$user_id', '$customer_name', '$email', '$address', '$total', 'pending', '$items_json')";

if ($conn->query($query)) {
    $order_id = $conn->insert_id;
    
    // Insert individual items
    foreach ($items as $item) {
        $food_name = $conn->real_escape_string($item['name']);
        $qty = (int)$item['quantity'];
        $conn->query("INSERT INTO items (order_id, food, qty) VALUES ('$order_id', '$food_name', '$qty')");
    }
    
    echo json_encode(['success' => true, 'message' => 'Order placed successfully!', 'total' => $total]);
} else {
    echo json_encode(['success' => false, 'message' => 'Order failed. Please try again.']);
}
?>
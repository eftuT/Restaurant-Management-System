<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
    exit;
}

$query = "SELECT * FROM food WHERE id = $id";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $item = $result->fetch_assoc();
    echo json_encode(['success' => true, 'item' => $item]);
} else {
    echo json_encode(['success' => false, 'message' => 'Item not found']);
}
?>
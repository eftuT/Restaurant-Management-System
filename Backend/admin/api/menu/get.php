<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../includes/db.php';

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
$query = "SELECT * FROM food WHERE is_available = 1";
if ($limit > 0) {
    $query .= " LIMIT $limit";
}
$result = $conn->query($query);

$menu = [];
while ($row = $result->fetch_assoc()) {
    $menu[] = $row;
}

echo json_encode(['success' => true, 'menu' => $menu]);
?>
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../includes/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

$full_name = $data['full_name'] ?? '';
$username = $data['username'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$phone = $data['phone'] ?? '';
$address = $data['address'] ?? '';

if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit;
}

$check = $conn->query("SELECT * FROM users WHERE email = '$email' OR username = '$username'");
if ($check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email or username already registered']);
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);
$query = "INSERT INTO users (full_name, username, email, password_hash, phone, address) 
          VALUES ('$full_name', '$username', '$email', '$password_hash', '$phone', '$address')";

if ($conn->query($query)) {
    echo json_encode(['success' => true, 'message' => 'Registration successful']);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed']);
}
?>
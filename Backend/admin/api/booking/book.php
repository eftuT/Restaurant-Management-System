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

$full_name = $data['full_name'] ?? '';
$email = $data['email'] ?? '';
$phone = $data['phone'] ?? '';
$booking_date = $data['booking_date'] ?? '';
$booking_time = $data['booking_time'] ?? '';
$number_of_people = $data['number_of_people'] ?? 1;
$table_type = $data['table_type'] ?? '';
$special_requests = $data['special_requests'] ?? '';

if (empty($full_name) || empty($email) || empty($phone) || empty($booking_date) || empty($booking_time)) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit;
}

$name_parts = explode(' ', $full_name, 2);
$fname = $name_parts[0];
$lname = $name_parts[1] ?? '';

$sql = "INSERT INTO reservation (fname, lname, guest, email, phone, date_res, time, suggestions) 
        VALUES ('$fname', '$lname', '$number_of_people', '$email', '$phone', '$booking_date', '$booking_time', '$special_requests')";

if ($conn->query($sql)) {
    // Also insert into tablebook for admin
    $sql2 = "INSERT INTO tablebook (Title, FName, LName, Email, Phone, Tbltyp, time, date, status) 
             VALUES ('', '$fname', '$lname', '$email', '$phone', '$table_type', '$booking_time', '$booking_date', 'NOT CONFIRM')";
    $conn->query($sql2);
    
    echo json_encode(['success' => true, 'message' => 'Booking confirmed! We will contact you shortly.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Booking failed. Please try again.']);
}
?>
<?php
echo "<h1>Image Test</h1>";

// Check database
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

$result = $conn->query("SELECT id, food_name, image_url FROM food");
echo "<h2>Database Image URLs:</h2>";
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . " - " . $row['food_name'] . " - image_url: " . ($row['image_url'] ?? 'NULL') . "<br>";
    
    // Check if image file exists
    if(!empty($row['image_url'])) {
        $fullPath = __DIR__ . '/' . $row['image_url'];
        if(file_exists($fullPath)) {
            echo "✅ Image file exists at: " . $fullPath . "<br>";
        } else {
            echo "❌ Image file NOT found at: " . $fullPath . "<br>";
        }
    }
}

// List all files in FoodPics
echo "<h2>Files in FoodPics folder:</h2>";
$foodPicsDir = __DIR__ . '/FoodPics/';
if (is_dir($foodPicsDir)) {
    $files = scandir($foodPicsDir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "📁 " . $file . "<br>";
        }
    }
} else {
    echo "❌ FoodPics folder does NOT exist!";
}
?>
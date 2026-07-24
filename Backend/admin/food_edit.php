<?php
session_start();
require_once __DIR__ . '/includes/db.php';
if(!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}

// Create FoodPics folder in the correct location (project root)
$foodPicsDir = __DIR__ . '/../../FoodPics/';
if (!file_exists($foodPicsDir)) {
    mkdir($foodPicsDir, 0777, true);
}

$msg = "";
$foodData = null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get food data
if($id > 0) {
    $result = $conn->query("SELECT * FROM food WHERE id = $id");
    if($result->num_rows > 0) {
        $foodData = $result->fetch_assoc();
    } else {
        header("location: food_list.php");
        exit;
    }
} else {
    header("location: food_list.php");
    exit;
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['submit'])) {
        $cat = htmlentities($_POST['category'], ENT_QUOTES, 'UTF-8');
        $name = htmlentities($_POST['name'], ENT_QUOTES, 'UTF-8');
        $price = htmlentities($_POST['price'], ENT_QUOTES, 'UTF-8');
        $desc = htmlentities($_POST['desc'], ENT_QUOTES, 'UTF-8');
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        $file = $_FILES['file'];
        $allowed_ext = array("jpg", "jpeg", "JPG", "JPEG", "png", "PNG", "gif", "GIF", "webp", "WEBP");
        
        if($cat != "" && $name != "" && $price != "" && $desc != "") {
            // Check for duplicate name (excluding current item)
            $check = $conn->query("SELECT * FROM food WHERE food_name='".$name."' AND id != $id LIMIT 1");
            if($check->num_rows) {
                $msg = "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> No duplicate food name allowed!</div>";
            } else {
                // Update food info
                $update = $conn->query("UPDATE food SET 
                    food_name = '$name', 
                    food_category = '$cat', 
                    food_price = '$price', 
                    food_description = '$desc',
                    is_available = $is_available
                    WHERE id = $id");
                
                if($update) {
                    // Handle image upload if new image is selected
                    if(!empty($file['name']) && $file['error'] == 0) {
                        $ext = explode(".", $_FILES['file']['name']);
                        $file_ext = strtolower(end($ext));
                        
                        if(in_array($file_ext, $allowed_ext)) {
                            // Delete old image if exists
                            if(!empty($foodData['image_url'])) {
                                $oldImage = __DIR__ . '/../../' . $foodData['image_url'];
                                if(file_exists($oldImage)) {
                                    unlink($oldImage);
                                }
                            }
                            
                            // Save new image
                            $image_name = $id . '.' . $file_ext;
                            $target_file = $foodPicsDir . $image_name;
                            $image_url = 'FoodPics/' . $image_name;
                            
                            if(move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                                $conn->query("UPDATE food SET image_url = '$image_url' WHERE id = $id");
                                $msg = "<div class='alert alert-success'><i class='fa fa-check-circle'></i> Food updated successfully with new image!</div>";
                            } else {
                                $msg = "<div class='alert alert-warning'><i class='fa fa-warning'></i> Food updated but image upload failed.</div>";
                            }
                        } else {
                            $msg = "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> Invalid image file format. Allowed: JPG, PNG, GIF, WEBP</div>";
                        }
                    } else {
                        $msg = "<div class='alert alert-success'><i class='fa fa-check-circle'></i> Food updated successfully!</div>";
                    }
                } else {
                    $msg = "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> Failed to update food record.</div>";
                }
            }
        } else {
            $msg = "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> All fields are required!</div>";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Food - SEN'Q</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #f0f2f5; }
        .top-nav { background: #1a1a2e; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; color: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.3); position: fixed; top: 0; left: 0; right: 0; z-index: 1000; }
        .top-nav .logo { font-size: 22px; font-weight: 700; }
        .top-nav .logo span { color: #f1c40f; }
        .sidebar { position: fixed; top: 70px; left: 0; bottom: 0; width: 250px; background: #16213e; padding: 20px 0; overflow-y: auto; z-index: 999; }
        .sidebar ul { list-style: none; padding: 0; margin: 0; }
        .sidebar ul li a { display: block; padding: 12px 25px; color: #a0aec0; text-decoration: none; transition: 0.3s; border-left: 3px solid transparent; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background: rgba(255,255,255,0.05); color: #fff; border-left-color: #f1c40f; }
        .sidebar ul li a i { margin-right: 12px; width: 20px; }
        .main-content { margin-left: 250px; margin-top: 70px; padding: 30px; min-height: calc(100vh - 70px); }
        .card { background: #fff; border-radius: 15px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .card .card-header { font-size: 18px; font-weight: 600; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f2f5; }
        .form-control { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 15px; transition: 0.3s; }
        .form-control:focus { border-color: #b45f2b; outline: none; box-shadow: 0 0 0 3px rgba(180, 95, 43, 0.1); }
        .btn-primary { background: linear-gradient(135deg, #b45f2b, #8a471f); color: #fff; padding: 12px 30px; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(180, 95, 43, 0.3); }
        .btn-secondary { background: #6c757d; color: #fff; padding: 12px 30px; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.3s; text-decoration: none; display: inline-block; }
        .btn-secondary:hover { background: #5a6268; transform: translateY(-2px); }
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .file-upload { border: 2px dashed #ddd; padding: 30px; text-align: center; border-radius: 10px; cursor: pointer; transition: 0.3s; background: #fafafa; }
        .file-upload:hover { border-color: #b45f2b; background: #fefcf3; }
        .file-upload i { font-size: 40px; color: #b45f2b; }
        .current-image { max-width: 150px; max-height: 150px; border-radius: 10px; margin: 10px 0; }
        .btn-group { display: flex; gap: 12px; margin-top: 20px; flex-wrap: wrap; }
        @media (max-width: 768px) { .sidebar { width: 60px; } .sidebar ul li a span { display: none; } .main-content { margin-left: 60px; } }
    </style>
</head>
<body>

<div class="top-nav">
    <div class="logo">🍽️ <span>SEN'Q</span> Admin</div>
    <div>
        <span><i class="fa fa-user-circle"></i> <?php echo $_SESSION['adminUser']; ?></span>
        <a href="logout.php" style="color:#e74c3c;margin-left:20px;text-decoration:none;">
            <i class="fa fa-sign-out"></i> Logout
        </a>
    </div>
</div>

<div class="sidebar">
    <ul>
        <li><a href="home.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
        <li><a href="addtable.php"><i class="fa fa-plus-circle"></i> <span>Add Table</span></a></li>
        <li><a href="settings.php"><i class="fa fa-table"></i> <span>Table Status</span></a></li>
        <li><a href="tabledel.php"><i class="fa fa-trash"></i> <span>Delete Table</span></a></li>
        <li><a href="food_add.php"><i class="fa fa-cutlery"></i> <span>Add Food</span></a></li>
        <li><a href="food_list.php" class="active"><i class="fa fa-list"></i> <span>Food List</span></a></li>
        <li><a href="reservations.php"><i class="fa fa-calendar"></i> <span>Reservations</span></a></li>
        <li><a href="orders.php"><i class="fa fa-shopping-cart"></i> <span>Orders</span></a></li>
        <li><a href="messages.php"><i class="fa fa-envelope"></i> <span>Newsletters</span></a></li>
        <li><a href="usersettings.php"><i class="fa fa-users"></i> <span>Admin Users</span></a></li>
        <li><a href="logout.php" style="color:#e74c3c;"><i class="fa fa-sign-out"></i> <span>Logout</span></a></li>
    </ul>
</div>

<div class="main-content">
    <h2 style="margin-bottom:25px;"><i class="fa fa-edit"></i> Edit Food Item</h2>
    
    <?php echo $msg; ?>

    <div class="card">
        <div class="card-header"><i class="fa fa-info-circle"></i> Edit Food Information</div>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Category *</label>
                <select name="category" class="form-control" required>
                    <option value="">Select Category</option>
                    <option value="breakfast" <?php echo $foodData['food_category'] == 'breakfast' ? 'selected' : ''; ?>>Breakfast</option>
                    <option value="lunch" <?php echo $foodData['food_category'] == 'lunch' ? 'selected' : ''; ?>>Lunch</option>
                    <option value="dinner" <?php echo $foodData['food_category'] == 'dinner' ? 'selected' : ''; ?>>Dinner</option>
                    <option value="snacks" <?php echo $foodData['food_category'] == 'snacks' ? 'selected' : ''; ?>>Snacks</option>
                    <option value="beverage" <?php echo $foodData['food_category'] == 'beverage' ? 'selected' : ''; ?>>Beverage</option>
                    <option value="dessert" <?php echo $foodData['food_category'] == 'dessert' ? 'selected' : ''; ?>>Dessert</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Food Name *</label>
                <input type="text" name="name" class="form-control" placeholder="Enter food name" required value="<?php echo htmlspecialchars($foodData['food_name']); ?>">
            </div>
            
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Price (in Birr) *</label>
                <input type="number" name="price" class="form-control" placeholder="Enter price" required min="0" step="0.01" value="<?php echo $foodData['food_price']; ?>">
            </div>
            
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Description *</label>
                <textarea name="desc" class="form-control" rows="4" placeholder="Enter food description" required><?php echo htmlspecialchars($foodData['food_description']); ?></textarea>
            </div>
            
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Availability</label>
                <div style="margin-top:10px;">
                    <label style="font-weight:400;display:inline-block;margin-right:20px;">
                        <input type="checkbox" name="is_available" value="1" <?php echo $foodData['is_available'] == 1 ? 'checked' : ''; ?>> Available
                    </label>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Current Image</label>
                <?php 
                $currentImage = '';
                if(!empty($foodData['image_url'])) {
                    $imagePath = '/Restaurant-Management-System/' . $foodData['image_url'];
                    if(file_exists(__DIR__ . '/../../' . $foodData['image_url'])) {
                        $currentImage = $imagePath;
                    }
                }
                ?>
                <?php if($currentImage): ?>
                    <div>
                        <img src="<?php echo $currentImage; ?>" alt="Current image" class="current-image">
                        <p style="color:#666;font-size:14px;">Current image: <?php echo basename($foodData['image_url']); ?></p>
                    </div>
                <?php else: ?>
                    <p style="color:#999;">No image uploaded</p>
                <?php endif; ?>
            </div>
            
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Change Image (optional)</label>
                <div class="file-upload" onclick="document.getElementById('fileInput').click()">
                    <i class="fa fa-cloud-upload"></i>
                    <p style="margin-top:10px;color:#888;">Click to upload new image (JPG, PNG, GIF, WEBP)</p>
                    <input type="file" id="fileInput" name="file" style="display:none;" accept="image/*">
                </div>
                <div id="fileName" style="margin-top:10px;color:#666;font-size:14px;"></div>
            </div>
            
            <div class="btn-group">
                <button type="submit" name="submit" class="btn-primary"><i class="fa fa-save"></i> Update Food</button>
                <a href="food_list.php" class="btn-secondary"><i class="fa fa-arrow-left"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('fileInput').addEventListener('change', function(e) {
    var fileName = e.target.files[0] ? e.target.files[0].name : 'No file selected';
    document.getElementById('fileName').textContent = 'Selected: ' + fileName;
});
</script>

</body>
</html>
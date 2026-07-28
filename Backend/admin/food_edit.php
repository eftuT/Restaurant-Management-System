<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require_once __DIR__ . '/includes/db.php';
$page_title = 'Edit Food';
require_once __DIR__ . '/includes/header.php';

// Create FoodPics folder if it doesn't exist
$foodPicsDir = __DIR__ . '/../../FoodPics/';
if (!file_exists($foodPicsDir)) {
    mkdir($foodPicsDir, 0777, true);
}

$msg = "";
$foodData = null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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
            $check = $conn->query("SELECT * FROM food WHERE food_name='".$name."' AND id != $id LIMIT 1");
            if($check->num_rows) {
                $msg = "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> No duplicate food name allowed!</div>";
            } else {
                $update = $conn->query("UPDATE food SET 
                    food_name = '$name', 
                    food_category = '$cat', 
                    food_price = '$price', 
                    food_description = '$desc',
                    is_available = $is_available
                    WHERE id = $id");
                
                if($update) {
                    if(!empty($file['name']) && $file['error'] == 0) {
                        $ext = explode(".", $_FILES['file']['name']);
                        $file_ext = strtolower(end($ext));
                        
                        if(in_array($file_ext, $allowed_ext)) {
                            if(!empty($foodData['image_url'])) {
                                $oldImage = __DIR__ . '/../../' . $foodData['image_url'];
                                if(file_exists($oldImage)) {
                                    unlink($oldImage);
                                }
                            }
                            
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
                            $msg = "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> Invalid image file format.</div>";
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

<style>
    /* Additional styles for edit page */
    .file-upload {
        border: 2px dashed #ddd;
        padding: 30px;
        text-align: center;
        border-radius: 10px;
        cursor: pointer;
        transition: 0.3s;
        background: #fafafa;
    }
    .file-upload:hover {
        border-color: #b45f2b;
        background: #fefcf3;
    }
    .file-upload i {
        font-size: 40px;
        color: #b45f2b;
    }
    .current-image {
        max-width: 150px;
        max-height: 150px;
        border-radius: 10px;
        margin: 10px 0;
    }
    .btn-secondary {
        background: #95a5a6;
        color: #fff;
        padding: 8px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: 0.3s;
        font-size: 14px;
        display: inline-block;
        text-decoration: none;
    }
    .btn-secondary:hover {
        background: #7f8c8d;
        transform: translateY(-2px);
        color: #fff;
    }
    .alert {
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e8e0d8;
        border-radius: 10px;
        font-size: 14px;
        transition: 0.3s;
        background: #fff !important;
        color: #2c1f16 !important;
    }
    .form-control:focus {
        border-color: #b45f2b;
        outline: none;
        box-shadow: 0 0 0 3px rgba(180,95,43,0.1);
        background: #fff !important;
    }
    select.form-control option {
        color: #2c1f16 !important;
        background: #fff !important;
        padding: 8px 12px !important;
    }
    select.form-control option:checked {
        background: #b45f2b !important;
        color: #fff !important;
    }
    .form-group {
        overflow: visible !important;
        margin-bottom: 20px;
    }
    .card-body {
        overflow: visible !important;
    }
</style>

<div class="main-content">
    <h2 style="margin-bottom:25px;color:#2c1f16;"><i class="fa fa-edit"></i> Edit Food Item</h2>
    
    <?php echo $msg; ?>

    <div class="card">
        <div class="card-header"><i class="fa fa-info-circle"></i> Edit Food Information</div>
        <div class="card-body" style="overflow:visible !important;">
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                
                <div class="form-group" style="overflow:visible !important;">
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
                
                <div class="form-group">
                    <label style="font-weight:600;">Food Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter food name" required value="<?php echo htmlspecialchars($foodData['food_name']); ?>">
                </div>
                
                <div class="form-group">
                    <label style="font-weight:600;">Price (in Birr) *</label>
                    <input type="number" name="price" class="form-control" placeholder="Enter price" required min="0" step="0.01" value="<?php echo $foodData['food_price']; ?>">
                </div>
                
                <div class="form-group">
                    <label style="font-weight:600;">Description *</label>
                    <textarea name="desc" class="form-control" rows="4" placeholder="Enter food description" required><?php echo htmlspecialchars($foodData['food_description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label style="font-weight:600;">Availability</label>
                    <div style="margin-top:10px;">
                        <label style="font-weight:400;display:inline-block;margin-right:20px;">
                            <input type="checkbox" name="is_available" value="1" <?php echo $foodData['is_available'] == 1 ? 'checked' : ''; ?>> Available
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
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
                
                <div class="form-group">
                    <label style="font-weight:600;">Change Image (optional)</label>
                    <div class="file-upload" onclick="document.getElementById('fileInput').click()">
                        <i class="fa fa-cloud-upload"></i>
                        <p style="margin-top:10px;color:#888;">Click to upload new image (JPG, PNG, GIF, WEBP)</p>
                        <input type="file" id="fileInput" name="file" style="display:none;" accept="image/*">
                    </div>
                    <div id="fileName" style="margin-top:10px;color:#666;font-size:14px;"></div>
                </div>
                
                <div style="display:flex;gap:12px;margin-top:20px;flex-wrap:wrap;">
                    <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update Food</button>
                    <a href="food_list.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('fileInput').addEventListener('change', function(e) {
    var fileName = e.target.files[0] ? e.target.files[0].name : 'No file selected';
    document.getElementById('fileName').textContent = 'Selected: ' + fileName;
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require_once __DIR__ . '/includes/db.php';
$page_title = 'Add Food';
require_once __DIR__ . '/includes/header.php';

// Create FoodPics folder if it doesn't exist
$foodPicsDir = __DIR__ . '/../../FoodPics/';
if (!file_exists($foodPicsDir)) {
    mkdir($foodPicsDir, 0777, true);
}

$msg = "";
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['submit']) && isset($_FILES['file'])) {
        $cat = htmlentities($_POST['category'], ENT_QUOTES, 'UTF-8');
        $name = htmlentities($_POST['name'], ENT_QUOTES, 'UTF-8');
        $price = htmlentities($_POST['price'], ENT_QUOTES, 'UTF-8');
        $desc = htmlentities($_POST['desc'], ENT_QUOTES, 'UTF-8');
        $file = $_FILES['file'];
        $allowed_ext = array("jpg", "jpeg", "JPG", "JPEG", "png", "PNG", "gif", "GIF", "webp", "WEBP");
        
        if($cat != "" && $name != "" && $price != "" && $desc != "" && empty($file) == false) {
            $ext = explode(".", $_FILES['file']['name']);
            $file_ext = strtolower(end($ext));
            
            if(in_array($file_ext, $allowed_ext)) {
                $check = $conn->query("SELECT * FROM food WHERE food_name='".$name."' LIMIT 1");
                if($check->num_rows) {
                    $msg = "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> No duplicate food name allowed!</div>";
                } else {
                    $insert = $conn->query("INSERT INTO food (food_name, food_category, food_price, food_description) VALUES('".$name."', '".$cat."', '".$price."', '".$desc."')");
                    if($insert) {
                        $ins_id = $conn->insert_id;
                        $image_name = $ins_id . '.' . $file_ext;
                        $target_file = $foodPicsDir . $image_name;
                        $image_url = 'FoodPics/' . $image_name;
                        
                        if(move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                            $conn->query("UPDATE food SET image_url = '$image_url' WHERE id = $ins_id");
                            $msg = "<div class='alert alert-success'><i class='fa fa-check-circle'></i> Food saved successfully with image!</div>";
                        } else {
                            $msg = "<div class='alert alert-warning'><i class='fa fa-warning'></i> Food saved but image upload failed.</div>";
                        }
                    } else {
                        $msg = "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> Failed to save food record.</div>";
                    }
                }
            } else {
                $msg = "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> Invalid image file format. Allowed: JPG, PNG, GIF, WEBP</div>";
            }
        } else {
            $msg = "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> All fields are required!</div>";
        }
    }
}
?>

<div class="main-content">
    <h2 style="margin-bottom:25px;color:#2c1f16;"><i class="fa fa-cutlery"></i> Add New Food Item</h2>
    
    <?php echo $msg; ?>

    <div class="card">
        <div class="card-header"><i class="fa fa-info-circle"></i> Food Information</div>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Category *</label>
                <select name="category" class="form-control" required>
                    <option value="">Select Category</option>
                    <option value="breakfast">Breakfast</option>
                    <option value="lunch">Lunch</option>
                    <option value="dinner">Dinner</option>
                    <option value="snacks">Snacks</option>
                    <option value="beverage">Beverage</option>
                    <option value="dessert">Dessert</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Food Name *</label>
                <input type="text" name="name" class="form-control" placeholder="Enter food name" required>
            </div>
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Price (in Birr) *</label>
                <input type="number" name="price" class="form-control" placeholder="Enter price" required min="0" step="0.01">
            </div>
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Description *</label>
                <textarea name="desc" class="form-control" rows="4" placeholder="Enter food description" required></textarea>
            </div>
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Food Image *</label>
                <div class="file-upload" style="border:2px dashed #ddd;padding:30px;text-align:center;border-radius:10px;cursor:pointer;transition:0.3s;background:#fafafa;" onclick="document.getElementById('fileInput').click()">
                    <i class="fa fa-cloud-upload" style="font-size:40px;color:#b45f2b;"></i>
                    <p style="margin-top:10px;color:#888;">Click to upload image (JPG, PNG, GIF, WEBP)</p>
                    <input type="file" id="fileInput" name="file" style="display:none;" required accept="image/*">
                </div>
                <div id="fileName" style="margin-top:10px;color:#666;font-size:14px;"></div>
            </div>
            <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Food</button>
        </form>
    </div>
</div>

<script>
document.getElementById('fileInput').addEventListener('change', function(e) {
    var fileName = e.target.files[0] ? e.target.files[0].name : 'No file selected';
    document.getElementById('fileName').textContent = 'Selected: ' + fileName;
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
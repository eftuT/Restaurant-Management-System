<?php
session_start();
require_once __DIR__ . '/includes/db.php';
if(!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
$msg = "";
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['submit']) && isset($_FILES['file'])) {
        $cat = htmlentities($_POST['category'], ENT_QUOTES, 'UTF-8');
        $name = htmlentities($_POST['name'], ENT_QUOTES, 'UTF-8');
        $price = htmlentities($_POST['price'], ENT_QUOTES, 'UTF-8');
        $desc = htmlentities($_POST['desc'], ENT_QUOTES, 'UTF-8');
        $file = $_FILES['file'];
        $allowed_ext = array("jpg", "jpeg", "JPG", "JPEG", "png", "PNG");
        if($cat != "" && $name != "" && $price != "" && $desc != "" && empty($file) == false) {
            $ext = explode(".", $_FILES['file']['name']);
            if(in_array($ext[1], $allowed_ext)) {
                $check = $conn->query("SELECT * FROM food WHERE food_name='".$name."' LIMIT 1");
                if($check->num_rows) {
                    $msg = "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> No duplicate food name allowed!</div>";
                } else {
                    $insert = $conn->query("INSERT INTO food(food_name, food_category, food_price, food_description) VALUES('".$name."', '".$cat."', '".$price."', '".$desc."')");
                    if($insert) {
                        $ins_id = $conn->insert_id;
                        $image_url = "../FoodPics/$ins_id.jpg";
                        if(move_uploaded_file($_FILES['file']['tmp_name'], $image_url)) {
                            $msg = "<div class='alert alert-success'><i class='fa fa-check-circle'></i> Food record successfully saved!</div>";
                        } else {
                            $msg = "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> Could not upload image</div>";
                        }
                    }
                }
            } else {
                $msg = "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> Invalid image file format (JPG, PNG only)</div>";
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
    <title>Add Food - SEN'Q</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #f0f2f5; }
        .top-nav {
            background: #1a1a2e;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .top-nav .logo { font-size: 22px; font-weight: 700; }
        .top-nav .logo span { color: #f1c40f; }
        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            bottom: 0;
            width: 250px;
            background: #16213e;
            padding: 20px 0;
            overflow-y: auto;
            z-index: 999;
        }
        .sidebar ul { list-style: none; padding: 0; margin: 0; }
        .sidebar ul li a {
            display: block;
            padding: 12px 25px;
            color: #a0aec0;
            text-decoration: none;
            transition: 0.3s;
            border-left: 3px solid transparent;
        }
        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: rgba(255,255,255,0.05);
            color: #fff;
            border-left-color: #f1c40f;
        }
        .sidebar ul li a i { margin-right: 12px; width: 20px; }
        .main-content {
            margin-left: 250px;
            margin-top: 70px;
            padding: 30px;
            min-height: calc(100vh - 70px);
        }
        .card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .card .card-header {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f2f5;
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: 0.3s;
        }
        .form-control:focus {
            border-color: #b45f2b;
            outline: none;
            box-shadow: 0 0 0 3px rgba(180, 95, 43, 0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #b45f2b, #8a471f);
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(180, 95, 43, 0.3);
        }
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .file-upload {
            border: 2px dashed #ddd;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
            background: #fafafa;
        }
        .file-upload:hover { border-color: #b45f2b; background: #fefcf3; }
        .file-upload i { font-size: 40px; color: #b45f2b; }
        @media (max-width: 768px) {
            .sidebar { width: 60px; }
            .sidebar ul li a span { display: none; }
            .main-content { margin-left: 60px; }
        }
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
        <li><a href="food_add.php" class="active"><i class="fa fa-cutlery"></i> <span>Add Food</span></a></li>
        <li><a href="food_list.php"><i class="fa fa-list"></i> <span>Food List</span></a></li>
        <li><a href="reservations.php"><i class="fa fa-calendar"></i> <span>Reservations</span></a></li>
        <li><a href="orders.php"><i class="fa fa-shopping-cart"></i> <span>Orders</span></a></li>
        <li><a href="messages.php"><i class="fa fa-envelope"></i> <span>Newsletters</span></a></li>
        <li><a href="usersettings.php"><i class="fa fa-users"></i> <span>Admin Users</span></a></li>
        <li><a href="logout.php" style="color:#e74c3c;"><i class="fa fa-sign-out"></i> <span>Logout</span></a></li>
    </ul>
</div>

<div class="main-content">
    <h2 style="margin-bottom:25px;"><i class="fa fa-cutlery"></i> Add New Food Item</h2>
    
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
                <div class="file-upload" onclick="document.getElementById('fileInput').click()">
                    <i class="fa fa-cloud-upload"></i>
                    <p style="margin-top:10px;color:#888;">Click to upload image (JPG or PNG)</p>
                    <input type="file" id="fileInput" name="file" style="display:none;" required accept="image/*">
                </div>
            </div>
            <button type="submit" name="submit" class="btn-primary"><i class="fa fa-save"></i> Save Food</button>
        </form>
    </div>
</div>

<script>
document.getElementById('fileInput').addEventListener('change', function(e) {
    var fileName = e.target.files[0] ? e.target.files[0].name : 'No file selected';
    document.querySelector('.file-upload p').textContent = 'Selected: ' + fileName;
});
</script>

</body>
</html>
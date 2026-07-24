<?php
session_start();
require_once __DIR__ . '/includes/db.php';
if(!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}

// Handle delete
if(isset($_GET['delete'])) {
    $delete = preg_replace("#[^0-9]#", "", $_GET['delete']);
    if($delete != "") {
        // Get image path to delete file
        $result = $conn->query("SELECT image_url FROM food WHERE id='$delete'");
        $row = $result->fetch_assoc();
        if(!empty($row['image_url'])) {
            $imagePath = __DIR__ . '/../../' . $row['image_url'];
            if(file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $conn->query("DELETE FROM food WHERE id='".$delete."'");
        echo "<script>alert('Food deleted successfully!'); window.location.href='food_list.php';</script>";
    }
}

$per_page = 10;
$count = $conn->query("SELECT * FROM food");
$pages = ceil((mysqli_num_rows($count)) / $per_page);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $per_page;
$cat = $conn->query("SELECT * FROM food LIMIT $start, $per_page");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Food List - SEN'Q</title>
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
        .table { width: 100%; border-collapse: collapse; }
        .table th { background: #f8f9fa; padding: 12px; text-align: left; font-weight: 600; }
        .table td { padding: 12px; border-bottom: 1px solid #eee; vertical-align: middle; }
        .table tr:hover { background: #f8f9fa; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-available { background: #d4edda; color: #155724; }
        .badge-unavailable { background: #f8d7da; color: #721c24; }
        .btn-sm { padding: 5px 12px; border: none; border-radius: 8px; cursor: pointer; transition: 0.3s; font-size: 12px; text-decoration: none; display: inline-block; }
        .btn-edit { background: #4a90d9; color: #fff; }
        .btn-edit:hover { background: #357abd; color: #fff; }
        .btn-delete { background: #e74c3c; color: #fff; }
        .btn-delete:hover { background: #c0392b; color: #fff; }
        .btn-add { background: #27ae60; color: #fff; padding: 10px 20px; border: none; border-radius: 10px; cursor: pointer; transition: 0.3s; text-decoration: none; display: inline-block; float: right; }
        .btn-add:hover { background: #1e8449; color: #fff; }
        .pagination { display: flex; gap: 5px; margin-top: 20px; flex-wrap: wrap; }
        .pagination a { padding: 8px 15px; background: #f0f2f5; border-radius: 8px; text-decoration: none; color: #333; transition: 0.3s; }
        .pagination a:hover, .pagination a.active { background: #b45f2b; color: #fff; }
        .empty-state { text-align: center; padding: 40px; color: #999; }
        .empty-state i { font-size: 50px; color: #ddd; display: block; margin-bottom: 20px; }
        .food-image { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; background: #e6dccc; }
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
    <div class="card">
        <div class="card-header">
            <i class="fa fa-utensils"></i> Food Items
            <a href="food_add.php" class="btn-add"><i class="fa fa-plus"></i> Add New</a>
        </div>
        
        <?php if($cat->num_rows > 0): ?>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php $x = $start + 1; while($row = $cat->fetch_assoc()): 
                    $imageSrc = '';
                    if(!empty($row['image_url'])) {
                        $imagePath = __DIR__ . '/../../' . $row['image_url'];
                        if(file_exists($imagePath)) {
                            $imageSrc = '/Restaurant-Management-System/' . $row['image_url'];
                        }
                    }
                ?>
                    <tr>
                        <td><?php echo $x++; ?></td>
                        <td>
                            <?php if($imageSrc): ?>
                                <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($row['food_name']); ?>" class="food-image">
                            <?php else: ?>
                                <div class="food-image" style="display:flex;align-items:center;justify-content:center;font-size:20px;color:#b45f2b;">
                                    <i class="fa fa-utensils"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($row['food_name']); ?></strong></td>
                        <td><span class="badge" style="background:#e9ecef;color:#495057;"><?php echo ucfirst($row['food_category']); ?></span></td>
                        <td><strong><?php echo $row['food_price']; ?> Br</strong></td>
                        <td>
                            <span class="badge <?php echo $row['is_available'] == 1 ? 'badge-available' : 'badge-unavailable'; ?>">
                                <?php echo $row['is_available'] == 1 ? 'Available' : 'Unavailable'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="food_edit.php?id=<?php echo $row['id']; ?>" class="btn-sm btn-edit">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="food_list.php?delete=<?php echo $row['id']; ?>" class="btn-sm btn-delete" onclick="return confirm('Delete this food item?')">
                                <i class="fa fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div class="pagination">
            <?php if($pages >= 1 && $page <= $pages): ?>
                <?php for($i = 1; $i <= $pages; $i++): ?>
                    <a href="food_list.php?page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fa fa-utensils"></i>
            <p>No food items added yet.</p>
            <a href="food_add.php" style="color:#b45f2b;">Add your first food item</a>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
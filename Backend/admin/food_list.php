<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require_once __DIR__ . '/includes/db.php';
$page_title = 'Food List';
require_once __DIR__ . '/includes/header.php';

// Handle delete
if(isset($_GET['delete'])) {
    $delete = preg_replace("#[^0-9]#", "", $_GET['delete']);
    if($delete != "") {
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

<div class="main-content">
    <div class="card">
        <div class="card-header">
            <i class="fa fa-utensils"></i> Food Items
            <a href="food_add.php" style="float:right;background:#27ae60;color:#fff;padding:6px 18px;border-radius:8px;text-decoration:none;font-size:13px;">
                <i class="fa fa-plus"></i> Add New
            </a>
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
                                <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($row['food_name']); ?>" style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                            <?php else: ?>
                                <div style="width:50px;height:50px;background:#e6dccc;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px;color:#b45f2b;">
                                    <i class="fa fa-utensils"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($row['food_name']); ?></strong></td>
                        <td><span class="badge" style="background:#e9ecef;color:#495057;"><?php echo ucfirst($row['food_category']); ?></span></td>
                        <td><strong><?php echo $row['food_price']; ?> Br</strong></td>
                        <td>
                            <span class="badge <?php echo $row['is_available'] == 1 ? 'badge-success' : 'badge-danger'; ?>">
                                <?php echo $row['is_available'] == 1 ? 'Available' : 'Unavailable'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="food_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="food_list.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this food item?')">
                                <i class="fa fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div style="display:flex;gap:5px;margin-top:20px;flex-wrap:wrap;">
            <?php if($pages >= 1 && $page <= $pages): ?>
                <?php for($i = 1; $i <= $pages; $i++): ?>
                    <a href="food_list.php?page=<?php echo $i; ?>" style="padding:8px 15px;background:<?php echo $i == $page ? '#b45f2b' : '#f0f2f5'; ?>;border-radius:8px;text-decoration:none;color:<?php echo $i == $page ? '#fff' : '#333'; ?>;">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div style="text-align:center;padding:40px;color:#999;">
            <i class="fa fa-utensils" style="font-size:50px;color:#ddd;display:block;margin-bottom:20px;"></i>
            <p>No food items added yet.</p>
            <a href="food_add.php" style="color:#b45f2b;">Add your first food item</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require_once __DIR__ . '/includes/db.php';
$page_title = 'Orders';
require_once __DIR__ . '/includes/header.php';

$per_page = 10;
$count = $conn->query("SELECT * FROM basket");
$pages = ceil((mysqli_num_rows($count)) / $per_page);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $per_page;
$orders = $conn->query("SELECT * FROM basket ORDER BY id DESC LIMIT $start, $per_page");
?>

<div class="main-content">
    <div class="card">
        <div class="card-header"><i class="fa fa-shopping-cart"></i> All Orders</div>
        <?php if($orders->num_rows > 0): ?>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = $orders->fetch_assoc()): 
                    $statusClass = $row['status'] == 'pending' ? 'badge-warning' : ($row['status'] == 'processing' ? 'badge-primary' : ($row['status'] == 'delivered' ? 'badge-success' : 'badge-danger'));
                ?>
                    <tr>
                        <td><strong>#<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></strong></td>
                        <td><?php echo $row['customer_name']; ?></td>
                        <td><strong><?php echo $row['total']; ?> Br</strong></td>
                        <td><span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                        <td><?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div style="display:flex;gap:5px;margin-top:20px;flex-wrap:wrap;">
            <?php if($pages >= 1 && $page <= $pages): ?>
                <?php for($i = 1; $i <= $pages; $i++): ?>
                    <a href="orders.php?page=<?php echo $i; ?>" style="padding:8px 15px;background:<?php echo $i == $page ? '#b45f2b' : '#f0f2f5'; ?>;border-radius:8px;text-decoration:none;color:<?php echo $i == $page ? '#fff' : '#333'; ?>;">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div style="text-align:center;padding:40px;color:#999;">
            <i class="fa fa-shopping-cart" style="font-size:50px;color:#ddd;display:block;margin-bottom:20px;"></i>
            <p>No orders yet.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
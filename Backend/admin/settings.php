<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require_once __DIR__ . '/includes/db.php';
$page_title = 'Table Status';
require_once __DIR__ . '/includes/header.php';
?>

<div class="main-content">
    <h2 style="margin-bottom:25px;color:#2c1f16;"><i class="fa fa-table"></i> Table Status</h2>
    
    <div class="stats-grid">
        <?php
        $query = "SELECT * FROM alltables";
        $res = $conn->query($query);
        while($row = $res->fetch_assoc()):
        ?>
        <div class="stat-card" style="border-left-color: <?php echo $row['status'] == 'Available' ? '#27ae60' : '#e74c3c'; ?>;">
            <div style="font-size:40px;color:#b45f2b;text-align:center;margin-bottom:10px;">
                <i class="fa fa-table"></i>
            </div>
            <div class="stat-number" style="text-align:center;font-size:24px;">Table #<?php echo $row['id']; ?></div>
            <div style="text-align:center;color:#666;"><?php echo $row['type']; ?></div>
            <div style="text-align:center;color:#888;font-size:14px;"><?php echo ucfirst($row['purpose']); ?></div>
            <div style="text-align:center;margin-top:10px;">
                <span class="badge <?php echo $row['status'] == 'Available' ? 'badge-success' : 'badge-danger'; ?>" style="font-size:14px;padding:6px 18px;">
                    <?php echo $row['status']; ?>
                </span>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
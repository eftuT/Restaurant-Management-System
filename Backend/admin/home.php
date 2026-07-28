<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require_once __DIR__ . '/includes/db.php';

$page_title = 'Dashboard';
require_once __DIR__ . '/includes/header.php';

$tableCount = $conn->query("SELECT COUNT(*) as count FROM alltables")->fetch_assoc()['count'];
$bookingCount = $conn->query("SELECT COUNT(*) as count FROM tablebook WHERE status='NOT CONFIRM'")->fetch_assoc()['count'];
$foodCount = $conn->query("SELECT COUNT(*) as count FROM food")->fetch_assoc()['count'];
$orderCount = $conn->query("SELECT COUNT(*) as count FROM basket")->fetch_assoc()['count'];
?>

<div class="main-content">
    <h2 style="margin-bottom:25px;color:#2c1f16;">Dashboard Overview</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo $tableCount; ?></div>
            <div class="stat-label"><i class="fa fa-table"></i> Total Tables</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $bookingCount; ?></div>
            <div class="stat-label"><i class="fa fa-calendar"></i> Pending Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $foodCount; ?></div>
            <div class="stat-label"><i class="fa fa-cutlery"></i> Food Items</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $orderCount; ?></div>
            <div class="stat-label"><i class="fa fa-shopping-cart"></i> Total Orders</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="fa fa-clock-o"></i> Recent Bookings</div>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Table</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $bookings = $conn->query("SELECT * FROM tablebook ORDER BY id DESC LIMIT 5");
                while($row = $bookings->fetch_assoc()):
                ?>
                    <tr>
                        <td><?php echo $row['FName'] . ' ' . $row['LName']; ?></td>
                        <td><?php echo $row['Tbltyp']; ?></td>
                        <td><?php echo $row['date']; ?></td>
                        <td><?php echo $row['time']; ?></td>
                        <td>
                            <span class="badge <?php echo $row['status'] == 'Confirm' ? 'badge-success' : 'badge-warning'; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="tablebook.php?rid=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fa fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
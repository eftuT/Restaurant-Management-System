<?php
require_once 'includes/header.php';
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$bookings = $conn->query("SELECT * FROM reservation WHERE email = '" . $_SESSION['user_email'] . "' ORDER BY date_res DESC LIMIT 5");
?>

<style>
    .dashboard { padding: 40px 0; }
    .dashboard h1 { font-size: 2.5rem; color: #2c1f16; margin-bottom: 10px; }
    .dashboard .subtitle { color: #666; margin-bottom: 30px; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: #fff; padding: 25px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 4px solid #b45f2b; }
    .stat-card .number { font-size: 32px; font-weight: 700; color: #b45f2b; }
    .stat-card .label { color: #666; font-size: 14px; }
    .card { background: #fff; border-radius: 15px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; border: 1px solid #f0ebe3; }
    .card .card-header { font-size: 18px; font-weight: 600; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f0ebe3; }
    .table { width: 100%; border-collapse: collapse; }
    .table th { background: #f8f5f0; padding: 10px; text-align: left; font-weight: 600; }
    .table td { padding: 10px; border-bottom: 1px solid #f0ebe3; }
    .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-success { background: #d4edda; color: #155724; }
    .badge-warning { background: #fff3cd; color: #856404; }
    .badge-danger { background: #f8d7da; color: #721c24; }
</style>

<section class="dashboard">
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <p class="subtitle">Manage your account and bookings</p>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="number"><?php echo $bookings->num_rows; ?></div>
                <div class="label"><i class="fas fa-calendar"></i> Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="number"><?php echo date('Y'); ?></div>
                <div class="label"><i class="fas fa-user"></i> Member Since</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="fas fa-user"></i> My Profile</div>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address'] ?? 'Not provided'); ?></p>
        </div>

        <div class="card">
            <div class="card-header"><i class="fas fa-calendar"></i> My Bookings</div>
            <?php if($bookings->num_rows > 0): ?>
            <div style="overflow-x:auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Guests</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($row = $bookings->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($row['date_res'])); ?></td>
                            <td><?php echo date('h:i A', strtotime($row['time'])); ?></td>
                            <td><?php echo $row['guest']; ?></td>
                            <td>
                                <span class="badge <?php echo strtotime($row['date_res']) > time() ? 'badge-success' : 'badge-warning'; ?>">
                                    <?php echo strtotime($row['date_res']) > time() ? 'Upcoming' : 'Past'; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p style="color:#999;">You have no bookings yet. <a href="booking.php" style="color:#b45f2b;">Book a table now</a></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
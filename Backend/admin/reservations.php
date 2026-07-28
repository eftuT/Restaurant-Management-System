<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require_once __DIR__ . '/includes/db.php';
$page_title = 'Reservations';
require_once __DIR__ . '/includes/header.php';

$per_page = 10;
$count = $conn->query("SELECT * FROM reservation");
$pages = ceil((mysqli_num_rows($count)) / $per_page);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $per_page;
$reserve = $conn->query("SELECT * FROM reservation ORDER BY date_res DESC, time DESC LIMIT $start, $per_page");

if(isset($_GET['delete'])) {
    $delete = preg_replace("#[^0-9]#", "", $_GET['delete']);
    if($delete != "") {
        $conn->query("DELETE FROM reservation WHERE reserve_id='".$delete."'");
        echo "<script>alert('Reservation deleted!'); window.location.href='reservations.php';</script>";
    }
}
?>

<div class="main-content">
    <div class="card">
        <div class="card-header"><i class="fa fa-calendar"></i> All Reservations</div>
        <?php if($reserve->num_rows > 0): ?>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Guests</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php $x = $start + 1; while($row = $reserve->fetch_assoc()): 
                    $today = date('Y-m-d');
                    $statusClass = $row['date_res'] == $today ? 'badge-warning' : ($row['date_res'] > $today ? 'badge-success' : 'badge-danger');
                    $statusText = $row['date_res'] == $today ? 'Today' : ($row['date_res'] > $today ? 'Upcoming' : 'Past');
                ?>
                    <tr>
                        <td><?php echo $x++; ?></td>
                        <td><strong><?php echo $row['fname'] . ' ' . $row['lname']; ?></strong></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><?php echo $row['guest']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['date_res'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($row['time'])); ?></td>
                        <td><span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
                        <td>
                            <a href="reservations.php?delete=<?php echo $row['reserve_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this reservation?')">
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
                    <a href="reservations.php?page=<?php echo $i; ?>" style="padding:8px 15px;background:<?php echo $i == $page ? '#b45f2b' : '#f0f2f5'; ?>;border-radius:8px;text-decoration:none;color:<?php echo $i == $page ? '#fff' : '#333'; ?>;">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div style="text-align:center;padding:40px;color:#999;">
            <i class="fa fa-calendar" style="font-size:50px;color:#ddd;display:block;margin-bottom:20px;"></i>
            <p>No reservations yet.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
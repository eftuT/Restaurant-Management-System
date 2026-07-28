<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require_once __DIR__ . '/includes/db.php';
$page_title = 'Confirm Booking';
require_once __DIR__ . '/includes/header.php';

if (!isset($_GET["rid"])) {
    header("location: index.php");
    exit;
}
$curdate = date("Y/m/d");
$id = $_GET["rid"];
$result = $conn->query("SELECT * FROM tablebook WHERE id = '$id'");
$row = $result->fetch_assoc();
$title = $row["Title"];
$fname = $row["FName"];
$lname = $row["LName"];
$email = $row["Email"];
$nation = $row["National"];
$country = $row["Country"];
$phone = $row["Phone"];
$tble = $row["Tbltyp"];
$purpose = $row["Purpose"];
$meal = $row["Meal"];
$time = $row["time"];
$date = $row["date"];
$status = $row["status"];

if (isset($_POST["confirm"])) {
    $status = $_POST["con"];
    if ($status == "Confirm") {
        $conn->query("UPDATE tablebook SET status='$status' WHERE id = '$id'");
        $notavail = "Booked";
        $conn->query("UPDATE alltables SET `status`='$notavail',`cid`='$id' WHERE purpose='$purpose' AND type='$tble'");
        echo "<script>alert('Booking Confirmed!'); window.location.href='tablebook.php?rid=$id';</script>";
    }
}
?>

<div class="main-content">
    <h2 style="margin-bottom:25px;color:#2c1f16;"><i class="fa fa-calendar-check-o"></i> Booking Confirmation</h2>

    <div class="card">
        <div class="card-header">
            <i class="fa fa-user"></i> Booking #<?php echo $id; ?>
            <a href="home.php" class="btn btn-secondary" style="float:right;background:#95a5a6;color:#fff;padding:6px 16px;border-radius:8px;text-decoration:none;font-size:13px;">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
        
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div style="padding:10px 0;border-bottom:1px solid #f0ebe3;"><strong>Full Name:</strong> <?php echo $title . $fname . ' ' . $lname; ?></div>
            <div style="padding:10px 0;border-bottom:1px solid #f0ebe3;"><strong>Email:</strong> <?php echo $email; ?></div>
            <div style="padding:10px 0;border-bottom:1px solid #f0ebe3;"><strong>Phone:</strong> <?php echo $phone; ?></div>
            <div style="padding:10px 0;border-bottom:1px solid #f0ebe3;"><strong>Nationality:</strong> <?php echo $nation; ?></div>
            <div style="padding:10px 0;border-bottom:1px solid #f0ebe3;"><strong>Country:</strong> <?php echo $country; ?></div>
            <div style="padding:10px 0;border-bottom:1px solid #f0ebe3;"><strong>Table Type:</strong> <?php echo $tble; ?></div>
            <div style="padding:10px 0;border-bottom:1px solid #f0ebe3;"><strong>Purpose:</strong> <?php echo $purpose; ?></div>
            <div style="padding:10px 0;border-bottom:1px solid #f0ebe3;"><strong>Meal Plan:</strong> <?php echo $meal; ?></div>
            <div style="padding:10px 0;border-bottom:1px solid #f0ebe3;"><strong>Date:</strong> <?php echo date('l, F d, Y', strtotime($date)); ?></div>
            <div style="padding:10px 0;border-bottom:1px solid #f0ebe3;"><strong>Time:</strong> <?php echo date('h:i A', strtotime($time)); ?></div>
            <div style="padding:10px 0;"><strong>Status:</strong> 
                <span class="badge <?php echo $status == 'Confirm' ? 'badge-success' : 'badge-warning'; ?>">
                    <?php echo $status; ?>
                </span>
            </div>
        </div>
    </div>

    <?php if($status != 'Confirm'): ?>
    <div class="card">
        <div class="card-header"><i class="fa fa-check-circle" style="color:#27ae60;"></i> Confirm Booking</div>
        <form method="post">
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Confirmation Action</label>
                <select name="con" class="form-control" required>
                    <option value="">Select Action</option>
                    <option value="Confirm">Confirm Booking</option>
                </select>
            </div>
            <button type="submit" name="confirm" class="btn btn-success" onclick="return confirm('Confirm this booking? This will mark the table as BOOKED.')">
                <i class="fa fa-check"></i> Confirm Booking
            </button>
        </form>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
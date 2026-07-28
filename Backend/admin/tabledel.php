<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require_once __DIR__ . '/includes/db.php';
$page_title = 'Delete Table';
require_once __DIR__ . '/includes/header.php';

if (isset($_POST["del"])) {
    $del = $_POST["id"];
    $conn->query("DELETE FROM alltables WHERE id = '$del'");
    $success = "Table Deleted Successfully!";
}
?>

<div class="main-content">
    <h2 style="margin-bottom:25px;color:#2c1f16;"><i class="fa fa-trash"></i> Delete Table</h2>
    
    <?php if(isset($success)): ?>
        <div class="alert alert-success" style="background:#d4edda;color:#155724;padding:15px;border-radius:10px;margin-bottom:20px;border:1px solid #c3e6cb;">
            <i class="fa fa-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><i class="fa fa-exclamation-triangle" style="color:#e74c3c;"></i> Select Table to Delete</div>
        <form method="post">
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Select Table ID *</label>
                <select name="id" class="form-control" required>
                    <option value="">-- Select Table --</option>
                    <?php
                    $query = "SELECT * FROM alltables";
                    $res = $conn->query($query);
                    while ($row = $res->fetch_assoc()) {
                        echo '<option value="'. $row["id"] . '">Table #' . $row["id"] . ' - ' . $row["type"] . ' (' . $row["status"] . ')</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" name="del" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this table?')">
                <i class="fa fa-trash"></i> Delete Table
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
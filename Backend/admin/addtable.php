<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require_once __DIR__ . '/includes/db.php';
$page_title = 'Add Table';
require_once __DIR__ . '/includes/header.php';

if (isset($_POST["add"])) {
    $typ = $_POST["tbltyp"];
    $purp = $_POST["purpose"];
    $status = "Available";
    $check = $conn->query("SELECT * FROM alltables WHERE type = '$typ' AND purpose = '$purp'");
    if ($check->num_rows > 0) {
        $error = "Table Already Exists!";
    } else {
        $conn->query("INSERT INTO alltables(type,purpose,status) VALUES ('$typ','$purp','$status')");
        $success = "Table Added Successfully!";
    }
}
?>

<div class="main-content">
    <h2 style="margin-bottom:25px;color:#2c1f16;"><i class="fa fa-plus-circle"></i> Add New Table</h2>
    
    <?php if(isset($success)): ?>
        <div class="alert alert-success" style="background:#d4edda;color:#155724;padding:15px;border-radius:10px;margin-bottom:20px;border:1px solid #c3e6cb;">
            <i class="fa fa-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>
    <?php if(isset($error)): ?>
        <div class="alert alert-danger" style="background:#f8d7da;color:#721c24;padding:15px;border-radius:10px;margin-bottom:20px;border:1px solid #f5c6cb;">
            <i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><i class="fa fa-info-circle"></i> Table Information</div>
        <form method="post">
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Type of Table *</label>
                <select name="tbltyp" class="form-control" required>
                    <option value="">Select Table Type</option>
                    <option value="Table for 2">Table for 2</option>
                    <option value="Table for 3">Table for 3</option>
                    <option value="Table for 4">Table for 4</option>
                    <option value="Table for 5">Table for 5</option>
                    <option value="Table for 6">Table for 6</option>
                    <option value="Table for 8">Table for 8</option>
                    <option value="Table for 10">Table for 10</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Purpose *</label>
                <select name="purpose" class="form-control" required>
                    <option value="">Select Purpose</option>
                    <option value="meeting">Meeting</option>
                    <option value="casual">Casual Dining</option>
                    <option value="celebration">Celebration</option>
                    <option value="business">Business</option>
                    <option value="family">Family</option>
                </select>
            </div>
            <button type="submit" name="add" class="btn btn-primary"><i class="fa fa-save"></i> Add Table</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header"><i class="fa fa-list"></i> Existing Tables</div>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Purpose</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $query = "SELECT * FROM alltables LIMIT 10";
                $res = $conn->query($query);
                while($row = $res->fetch_assoc()):
                ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['type']; ?></td>
                        <td><?php echo ucfirst($row['purpose']); ?></td>
                        <td>
                            <span class="badge <?php echo $row['status'] == 'Available' ? 'badge-success' : 'badge-danger'; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require_once __DIR__ . '/includes/db.php';
$page_title = 'Admin Users';
require_once __DIR__ . '/includes/header.php';

if (isset($_POST["ad"])) {
    $unam = $_POST["newun"];
    $passd = $_POST["newps"];
    $conn->query("INSERT INTO login(uname,pass) VALUES('$unam','$passd')");
    $success = "New admin user added!";
}
if (isset($_POST["upd"])) {
    $una = $_POST["uname"];
    $pasw = $_POST["pass"];
    $conn->query("UPDATE login SET uname = '$una', pass = '$pasw' WHERE id = '$id'");
    $success = "Admin user updated!";
}
?>

<div class="main-content">
    <h2 style="margin-bottom:25px;color:#2c1f16;"><i class="fa fa-users"></i> Admin User Management</h2>
    
    <?php if(isset($success)): ?>
        <div class="alert alert-success" style="background:#d4edda;color:#155724;padding:15px;border-radius:10px;margin-bottom:20px;border:1px solid #c3e6cb;">
            <i class="fa fa-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <i class="fa fa-user-plus"></i> Add New Admin
            <button onclick="document.getElementById('addModal').classList.add('show')" style="float:right;background:#27ae60;color:#fff;padding:5px 15px;border:none;border-radius:8px;cursor:pointer;">
                <i class="fa fa-plus"></i> Add Admin
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="fa fa-list"></i> Admin Users List</div>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $result = $conn->query("SELECT * FROM login");
                while($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><strong><?php echo $row['uname']; ?></strong></td>
                        <td>
                            <button onclick="openUpdateModal('<?php echo $row['id']; ?>', '<?php echo $row['uname']; ?>')" class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                            <a href="usersettingsdel.php?eid=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this admin user?')">
                                <i class="fa fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Admin Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <h3><i class="fa fa-user-plus"></i> Add New Admin</h3>
        <form method="post">
            <div class="form-group" style="margin-bottom:15px;">
                <label style="font-weight:600;">Username</label>
                <input name="newun" class="form-control" placeholder="Enter username" required>
            </div>
            <div class="form-group" style="margin-bottom:15px;">
                <label style="font-weight:600;">Password</label>
                <input name="newps" type="password" class="form-control" placeholder="Enter password" required>
            </div>
            <button type="submit" name="ad" class="btn btn-success"><i class="fa fa-save"></i> Add Admin</button>
            <button type="button" onclick="document.getElementById('addModal').classList.remove('show')" class="btn btn-danger" style="margin-left:10px;">Cancel</button>
        </form>
    </div>
</div>

<!-- Update Admin Modal -->
<div id="updateModal" class="modal">
    <div class="modal-content">
        <h3><i class="fa fa-edit"></i> Update Admin</h3>
        <form method="post" id="updateForm">
            <input type="hidden" name="update_id" id="update_id">
            <div class="form-group" style="margin-bottom:15px;">
                <label style="font-weight:600;">Username</label>
                <input name="uname" id="update_uname" class="form-control" placeholder="Enter username" required>
            </div>
            <div class="form-group" style="margin-bottom:15px;">
                <label style="font-weight:600;">Password</label>
                <input name="pass" type="password" class="form-control" placeholder="Enter new password" required>
            </div>
            <button type="submit" name="upd" class="btn btn-primary"><i class="fa fa-save"></i> Update Admin</button>
            <button type="button" onclick="document.getElementById('updateModal').classList.remove('show')" class="btn btn-danger" style="margin-left:10px;">Cancel</button>
        </form>
    </div>
</div>

<script>
function openUpdateModal(id, username) {
    document.getElementById('update_id').value = id;
    document.getElementById('update_uname').value = username;
    document.getElementById('updateModal').classList.add('show');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
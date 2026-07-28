<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require_once __DIR__ . '/includes/db.php';
$page_title = 'Newsletters';
require_once __DIR__ . '/includes/header.php';

if (isset($_POST["log"])) {
    $title = $_POST["title"];
    $subject = $_POST["subject"];
    $news = $_POST["news"];
    $conn->query("INSERT INTO newsletterlog(title,subject,news) VALUES('$title','$subject','$news')");
    $success = "Newsletter sent successfully!";
}
?>

<div class="main-content">
    <h2 style="margin-bottom:25px;color:#2c1f16;"><i class="fa fa-envelope"></i> Newsletter Management</h2>
    
    <?php if(isset($success)): ?>
        <div class="alert alert-success" style="background:#d4edda;color:#155724;padding:15px;border-radius:10px;margin-bottom:20px;border:1px solid #c3e6cb;">
            <i class="fa fa-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><i class="fa fa-pencil"></i> Compose Newsletter</div>
        <form method="post">
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Title *</label>
                <input name="title" class="form-control" placeholder="Enter newsletter title" required>
            </div>
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Subject *</label>
                <input name="subject" class="form-control" placeholder="Enter email subject" required>
            </div>
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600;">Message *</label>
                <textarea name="news" class="form-control" rows="6" placeholder="Write your newsletter content..." required></textarea>
            </div>
            <button type="submit" name="log" class="btn btn-primary"><i class="fa fa-send"></i> Send Newsletter</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header"><i class="fa fa-users"></i> Subscribers List</div>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $res = $conn->query("SELECT * FROM contact");
                while($row = $res->fetch_assoc()):
                ?>
                    <tr>
                        <td><?php echo $row['fullname']; ?></td>
                        <td><?php echo $row['phoneno']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td>
                            <span class="badge <?php echo $row['approval'] == 'Allowed' ? 'badge-success' : 'badge-danger'; ?>">
                                <?php echo $row['approval']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="newsletter.php?eid=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">
                                <i class="fa fa-check"></i> Toggle
                            </a>
                            <a href="newsletterdel.php?eid=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Remove this subscriber?')">
                                <i class="fa fa-trash"></i>
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
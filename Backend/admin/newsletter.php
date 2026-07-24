<?php
session_start();
if (!isset($_SESSION['adminLoggedIn'])) {
    header("location: index.php");
    exit;
}
require_once __DIR__ . '/includes/db.php';

$eid = $_GET["eid"];
$approval = "Allowed";
$napproval = "Not Allowed";

$query = "SELECT * FROM contact where id = '$eid'";
$res = $conn->query($query);
$row = $res->fetch_assoc();
$id = $row["approval"];

if($id == "Not Allowed") {
    $conn->query("UPDATE contact SET `approval`= '$approval' WHERE id = '$eid'");
    $message = "Subscriber approved!";
} else {
    $conn->query("UPDATE contact SET `approval`= '$napproval' WHERE id = '$eid'");
    $message = "Subscriber blocked!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Newsletter Action - SEN'Q</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <style>
        body { background: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .card {
            background: #fff;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .icon { font-size: 60px; margin-bottom: 20px; }
        .icon.success { color: #27ae60; }
        .icon.info { color: #4a90d9; }
        h2 { color: #1a1a2e; margin-bottom: 10px; }
        p { color: #666; margin-bottom: 20px; }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary { background: #b45f2b; color: #fff; }
        .btn-primary:hover { background: #8a471f; transform: scale(1.05); }
        .btn-success { background: #27ae60; color: #fff; }
        .btn-success:hover { background: #1e8449; transform: scale(1.05); }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon <?php echo strpos($message, 'approved') !== false ? 'success' : 'info'; ?>">
            <i class="fa <?php echo strpos($message, 'approved') !== false ? 'fa-check-circle' : 'fa-info-circle'; ?>"></i>
        </div>
        <h2><?php echo $message; ?></h2>
        <p>Redirecting to newsletter management...</p>
        <a href="messages.php" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back to Newsletters</a>
    </div>
    <script>
        setTimeout(function() {
            window.location.href = 'messages.php';
        }, 1500);
    </script>
</body>
</html>
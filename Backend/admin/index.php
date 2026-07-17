<?php
session_start();
require "../includes/db.php";
if(isset($_SESSION['adminLoggedIn'])) {
    header("location: home.php");
}
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if($username != "" && $password != "") {
        $query = "SELECT * FROM login WHERE uname='$username' AND pass='$password'";
        $result = $conn->query($query);
        if($result->num_rows > 0) {
            $_SESSION['adminLoggedIn'] = true;
            $_SESSION['adminUser'] = $username;
            header("location: home.php");
            exit;
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - SEN'Q</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <style>
        .login-wrapper { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #2c1f16; }
        .login-box { background: #fff; padding: 40px; border-radius: 10px; width: 350px; box-shadow: 0 0 30px rgba(0,0,0,0.3); }
        .login-box h2 { text-align: center; margin-bottom: 30px; color: #b45f2b; }
        .login-box input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        .login-box button { width: 100%; padding: 12px; background: #b45f2b; color: #fff; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        .login-box button:hover { background: #8a471f; }
        .error { color: red; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-box">
            <h2>SEN'Q Admin</h2>
            <?php if(isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
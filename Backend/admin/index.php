<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if(isset($_SESSION['adminLoggedIn'])) {
    header("location: home.php");
    exit;
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
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login - SEN'Q</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #fefcf3; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .login-box {
            background: #fff;
            padding: 50px 40px;
            border-radius: 20px;
            width: 400px;
            box-shadow: 0 10px 40px rgba(44,31,22,0.08);
            border: 1px solid #e8e0d8;
        }
        .login-box .logo { text-align: center; margin-bottom: 30px; }
        .login-box .logo .icon { 
            background: #b45f2b; 
            color: #fff; 
            width: 70px; 
            height: 70px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 32px; 
            font-weight: bold; 
            margin: 0 auto 15px;
        }
        .login-box .logo h2 { color: #2c1f16; font-size: 28px; }
        .login-box .logo h2 span { color: #b45f2b; }
        .login-box .logo p { color: #999; font-size: 14px; }
        .login-box .form-group { margin-bottom: 20px; }
        .login-box label { font-weight: 600; color: #2c1f16; display: block; margin-bottom: 5px; }
        .login-box input { 
            width: 100%; 
            padding: 12px 16px; 
            border: 2px solid #e8e0d8; 
            border-radius: 10px; 
            font-size: 15px; 
            transition: 0.3s; 
            background: #faf8f5;
        }
        .login-box input:focus { 
            border-color: #b45f2b; 
            outline: none; 
            box-shadow: 0 0 0 3px rgba(180,95,43,0.08);
            background: #fff;
        }
        .login-box .btn { 
            width: 100%; 
            padding: 14px; 
            background: #b45f2b; 
            color: #fff; 
            border: none; 
            border-radius: 10px; 
            font-size: 16px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: 0.3s; 
        }
        .login-box .btn:hover { 
            background: #8a471f; 
            transform: translateY(-2px); 
            box-shadow: 0 8px 25px rgba(180,95,43,0.2);
        }
        .login-box .error { 
            background: #fdf0ed; 
            color: #c0392b; 
            padding: 12px 16px; 
            border-radius: 10px; 
            margin-bottom: 20px; 
            text-align: center; 
            border-left: 4px solid #c0392b;
        }
        .login-box .footer-text { text-align: center; margin-top: 20px; color: #999; font-size: 13px; }
        .login-box .footer-text a { color: #b45f2b; text-decoration: none; }
        .login-box .footer-text a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">
            <div class="icon">S</div>
            <h2>SEN'Q <span>Admin</span></h2>
            <p>Restaurant Management System</p>
        </div>
        <?php if(isset($error)): ?>
            <div class="error"><i class="fa fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label><i class="fa fa-user"></i> Username</label>
                <input type="text" name="username" placeholder="Enter your username" required>
            </div>
            <div class="form-group">
                <label><i class="fa fa-lock"></i> Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn"><i class="fa fa-sign-in"></i> Login</button>
            <div class="footer-text">
                <p>Default: <strong>admin</strong> / <strong>admin123</strong></p>
            </div>
        </form>
    </div>
</body>
</html>
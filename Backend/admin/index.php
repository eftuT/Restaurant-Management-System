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
    <title>Admin Login - SEN'Q Restaurant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #1a1a2e; }
        .login-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        }
        .login-box {
            background: #fff;
            padding: 50px 40px;
            border-radius: 20px;
            width: 400px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
            animation: slideUp 0.6s ease;
        }
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .login-box .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-box .logo h2 {
            color: #b45f2b;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .login-box .logo h2 span {
            color: #f1c40f;
        }
        .login-box .logo p {
            color: #999;
            font-size: 14px;
        }
        .login-box .form-group {
            margin-bottom: 20px;
        }
        .login-box label {
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }
        .login-box input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: 0.3s;
        }
        .login-box input:focus {
            border-color: #b45f2b;
            outline: none;
            box-shadow: 0 0 0 3px rgba(180, 95, 43, 0.1);
        }
        .login-box .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #b45f2b, #8a471f);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .login-box .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(180, 95, 43, 0.3);
        }
        .login-box .btn-login i {
            margin-right: 8px;
        }
        .login-box .error {
            background: #fee;
            color: #c00;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #c00;
        }
        .login-box .footer-text {
            text-align: center;
            margin-top: 20px;
            color: #999;
            font-size: 13px;
        }
        .login-box .footer-text a {
            color: #b45f2b;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-box">
            <div class="logo">
                <h2>🍽️ <span>SEN'Q</span></h2>
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
                <button type="submit" class="btn-login"><i class="fa fa-sign-in"></i> Login</button>
                <div class="footer-text">
                    <p>Default: <strong>admin</strong> / <strong>admin123</strong></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
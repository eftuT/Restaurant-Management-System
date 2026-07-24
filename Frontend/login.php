<?php
session_start();
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

if(isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if(empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if(password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid password';
            }
        } else {
            $error = 'User not found. Please sign up first.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SEN'Q Restaurant</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;400;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4a085e9b53.js" crossorigin="anonymous"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        .auth-page { display: flex; justify-content: center; align-items: center; min-height: 80vh; background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460); padding: 30px 20px; }
        .auth-box { background: #fff; padding: 40px 35px; border-radius: 20px; max-width: 420px; width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.4); }
        .auth-box .logo { text-align: center; margin-bottom: 20px; }
        .auth-box .logo h2 { color: #2c1f16; font-size: 28px; }
        .auth-box .logo h2 span { color: #b45f2b; }
        .auth-box label { font-weight: 600; display: block; margin-top: 12px; }
        .auth-box input { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; margin-top: 4px; font-size: 15px; transition: 0.3s; }
        .auth-box input:focus { border-color: #b45f2b; outline: none; box-shadow: 0 0 0 3px rgba(180,95,43,0.1); }
        .auth-box .btn { width: 100%; margin-top: 20px; padding: 14px; border: none; border-radius: 10px; background: linear-gradient(135deg, #b45f2b, #8a471f); color: #fff; font-weight: 600; font-size: 16px; cursor: pointer; transition: 0.3s; }
        .auth-box .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(180,95,43,0.3); }
        .auth-box .links { text-align: center; margin-top: 20px; }
        .auth-box .links a { color: #b45f2b; font-weight: 600; text-decoration: none; }
        .auth-box .error { background: #fee; color: #c00; padding: 12px; border-radius: 10px; margin-bottom: 15px; text-align: center; border-left: 4px solid #c00; }
    </style>
</head>
<body>

<div class="auth-page">
    <div class="auth-box">
        <div class="logo">
            <div style="background:#b45f2b;color:#fff;width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:bold;margin:0 auto 10px;">S</div>
            <h2>SEN'Q <span>Restaurant</span></h2>
            <p style="color:#666;">Welcome back!</p>
        </div>
        
        <?php if($error): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Email</label>
            <input type="email" name="email" placeholder="Enter your email" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>

            <button type="submit" class="btn"><i class="fas fa-sign-in-alt"></i> Login</button>

            <div class="links">
                Don't have an account? <a href="signup.php">Sign up now</a>
            </div>
            <div class="links" style="margin-top:10px;">
                <a href="index.php" style="color:#999;font-weight:400;font-size:14px;"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
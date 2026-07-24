<?php
session_start();
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

if(isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    
    if(empty($full_name) || empty($username) || empty($email) || empty($password)) {
        $error = 'All required fields must be filled';
    } elseif($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif(strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $error = 'Email or username already registered';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password_hash, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $full_name, $username, $email, $password_hash, $phone, $address);
            
            if($stmt->execute()) {
                $success = 'Registration successful! Please login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - SEN'Q Restaurant</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;400;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4a085e9b53.js" crossorigin="anonymous"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        .auth-page { display: flex; justify-content: center; align-items: center; min-height: 80vh; background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460); padding: 30px 20px; }
        .auth-box { background: #fff; padding: 40px 35px; border-radius: 20px; max-width: 460px; width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.4); }
        .auth-box .logo { text-align: center; margin-bottom: 20px; }
        .auth-box .logo h2 { color: #2c1f16; font-size: 28px; }
        .auth-box .logo h2 span { color: #b45f2b; }
        .auth-box label { font-weight: 600; display: block; margin-top: 10px; }
        .auth-box input { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; margin-top: 4px; font-size: 15px; transition: 0.3s; }
        .auth-box input:focus { border-color: #b45f2b; outline: none; box-shadow: 0 0 0 3px rgba(180,95,43,0.1); }
        .auth-box .btn { width: 100%; margin-top: 20px; padding: 14px; border: none; border-radius: 10px; background: linear-gradient(135deg, #b45f2b, #8a471f); color: #fff; font-weight: 600; font-size: 16px; cursor: pointer; transition: 0.3s; }
        .auth-box .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(180,95,43,0.3); }
        .auth-box .links { text-align: center; margin-top: 20px; }
        .auth-box .links a { color: #b45f2b; font-weight: 600; text-decoration: none; }
        .auth-box .error { background: #fee; color: #c00; padding: 12px; border-radius: 10px; margin-bottom: 15px; text-align: center; border-left: 4px solid #c00; }
        .auth-box .success { background: #efe; color: #060; padding: 12px; border-radius: 10px; margin-bottom: 15px; text-align: center; border-left: 4px solid #060; }
    </style>
</head>
<body>

<div class="auth-page">
    <div class="auth-box">
        <div class="logo">
            <div style="background:#b45f2b;color:#fff;width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:bold;margin:0 auto 10px;">S</div>
            <h2>SEN'Q <span>Restaurant</span></h2>
            <p style="color:#666;">Create your account</p>
        </div>
        
        <?php if($error): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Full Name *</label>
            <input type="text" name="full_name" placeholder="Enter your full name" required>

            <label>Username *</label>
            <input type="text" name="username" placeholder="Choose a username" required>

            <label>Email *</label>
            <input type="email" name="email" placeholder="Enter your email" required>

            <label>Password *</label>
            <input type="password" name="password" placeholder="Create a password (min 6 chars)" required minlength="6">

            <label>Confirm Password *</label>
            <input type="password" name="confirm_password" placeholder="Confirm your password" required>

            <label>Phone (optional)</label>
            <input type="tel" name="phone" placeholder="Enter your phone number">

            <label>Address (optional)</label>
            <input type="text" name="address" placeholder="Enter your address">

            <div style="margin-top:12px;">
                <label><input type="checkbox" required> I agree to the <a href="#" style="color:#b45f2b;">Terms & Privacy</a></label>
            </div>

            <button type="submit" class="btn"><i class="fas fa-user-plus"></i> Create Account</button>

            <div class="links">
                Already have an account? <a href="login.php">Log in</a>
            </div>
            <div class="links" style="margin-top:10px;">
                <a href="index.php" style="color:#999;font-weight:400;font-size:14px;"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
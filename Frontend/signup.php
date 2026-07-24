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
        body { 
            background: #fefcf3; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 40px 20px;
        }
        
        .auth-box {
            background: #ffffff;
            padding: 50px 45px;
            border-radius: 24px;
            max-width: 560px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
            border: 1px solid #f0ebe3;
        }
        .auth-box .logo { 
            text-align: center; 
            margin-bottom: 30px; 
        }
        .auth-box .logo .logo-icon { 
            background: #b45f2b; 
            color: #fff; 
            width: 65px; 
            height: 65px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 28px; 
            font-weight: bold; 
            margin: 0 auto 12px;
        }
        .auth-box .logo h2 { 
            color: #2c1f16; 
            font-size: 26px; 
        }
        .auth-box .logo h2 span { 
            color: #b45f2b; 
        }
        .auth-box .logo p { 
            color: #999; 
            font-size: 14px; 
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .form-row .full-width {
            grid-column: 1 / -1;
        }
        
        .auth-box label { 
            font-weight: 600; 
            display: block; 
            margin-top: 14px; 
            color: #2c1f16; 
            font-size: 14px;
        }
        .auth-box label .required {
            color: #e74c3c;
            margin-left: 2px;
        }
        .auth-box input { 
            width: 100%; 
            padding: 13px 16px; 
            border: 2px solid #e8e0d8; 
            border-radius: 12px; 
            margin-top: 5px; 
            font-size: 14px; 
            transition: 0.3s; 
            background: #faf8f5;
        }
        .auth-box input:focus { 
            border-color: #b45f2b; 
            outline: none; 
            box-shadow: 0 0 0 4px rgba(180,95,43,0.08);
            background: #ffffff;
        }
        
        .auth-box .btn { 
            width: 100%; 
            margin-top: 25px; 
            padding: 16px; 
            border: none; 
            border-radius: 12px; 
            background: #b45f2b; 
            color: #fff; 
            font-weight: 600; 
            font-size: 16px; 
            cursor: pointer; 
            transition: 0.3s; 
        }
        .auth-box .btn:hover { 
            background: #8a471f; 
            transform: translateY(-2px); 
            box-shadow: 0 8px 25px rgba(180,95,43,0.25);
        }
        
        .auth-box .links { 
            text-align: center; 
            margin-top: 20px; 
            font-size: 15px;
        }
        .auth-box .links a { 
            color: #b45f2b; 
            font-weight: 600; 
            text-decoration: none; 
        }
        .auth-box .links a:hover { 
            text-decoration: underline; 
        }
        
        .auth-box .error { 
            background: #fdf0ed; 
            color: #c0392b; 
            padding: 12px 16px; 
            border-radius: 12px; 
            margin-bottom: 20px; 
            text-align: center; 
            border-left: 4px solid #c0392b;
            font-size: 14px;
        }
        .auth-box .success { 
            background: #edf7ed; 
            color: #1e8449; 
            padding: 12px 16px; 
            border-radius: 12px; 
            margin-bottom: 20px; 
            text-align: center; 
            border-left: 4px solid #1e8449;
            font-size: 14px;
        }
        
        .auth-box .terms { 
            font-size: 0.85rem; 
            color: #666; 
            margin-top: 16px; 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }
        .auth-box .terms input[type="checkbox"] { 
            width: 18px; 
            height: 18px; 
            margin-top: 0; 
            flex-shrink: 0;
        }
        .auth-box .terms a { 
            color: #b45f2b; 
            text-decoration: none; 
        }
        .auth-box .terms a:hover { 
            text-decoration: underline; 
        }
        
        .auth-footer { 
            text-align: center; 
            margin-top: 22px; 
            padding-top: 18px; 
            border-top: 1px solid #f0ebe3; 
        }
        .auth-footer a { 
            color: #999; 
            text-decoration: none; 
            font-size: 14px; 
        }
        .auth-footer a:hover { 
            color: #b45f2b; 
        }
        
        @media (max-width: 520px) {
            .auth-box { padding: 30px 20px; }
            .form-row {
                grid-template-columns: 1fr;
            }
            .form-row .full-width {
                grid-column: 1;
            }
        }
    </style>
</head>
<body>

<div class="auth-box">
    <div class="logo">
        <div class="logo-icon">S</div>
        <h2>SEN'Q <span>Restaurant</span></h2>
        <p>Create your account and start ordering</p>
    </div>
    
    <?php if($error): ?>
        <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-row">
            <div class="full-width">
                <label>Full Name <span class="required">*</span></label>
                <input type="text" name="full_name" placeholder="Enter your full name" required>
            </div>
        </div>

        <div class="form-row">
            <div>
                <label>Username <span class="required">*</span></label>
                <input type="text" name="username" placeholder="Choose a username" required>
            </div>
            <div>
                <label>Email Address <span class="required">*</span></label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
        </div>

        <div class="form-row">
            <div>
                <label>Password <span class="required">*</span></label>
                <input type="password" name="password" placeholder="Create a password" required minlength="6">
            </div>
            <div>
                <label>Confirm Password <span class="required">*</span></label>
                <input type="password" name="confirm_password" placeholder="Confirm your password" required>
            </div>
        </div>

        <div class="form-row">
            <div>
                <label>Phone Number</label>
                <input type="tel" name="phone" placeholder="Enter your phone number">
            </div>
            <div>
                <label>Address</label>
                <input type="text" name="address" placeholder="Enter your address">
            </div>
        </div>

        <div class="terms">
            <input type="checkbox" required>
            <span>I agree to the <a href="#">Terms & Privacy</a></span>
        </div>

        <button type="submit" class="btn"><i class="fas fa-user-plus"></i> Create Account</button>

        <div class="links">
            Already have an account? <a href="login.php">Log in</a>
        </div>
        <div class="auth-footer">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
    </form>
</div>

</body>
</html>
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
        body { 
            background: #fefcf3; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 20px;
        }
        
        .auth-box {
            background: #ffffff;
            padding: 50px 40px;
            border-radius: 20px;
            max-width: 440px;
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
        .auth-box .logo h2 { 
            color: #2c1f16; 
            font-size: 28px; 
        }
        .auth-box .logo h2 span { 
            color: #b45f2b; 
        }
        .auth-box .logo p { 
            color: #999; 
            font-size: 14px; 
        }
        
        .auth-box label { 
            font-weight: 600; 
            display: block; 
            margin-top: 16px; 
            color: #2c1f16; 
        }
        .auth-box input { 
            width: 100%; 
            padding: 14px 18px; 
            border: 2px solid #e8e0d8; 
            border-radius: 12px; 
            margin-top: 6px; 
            font-size: 15px; 
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
            padding: 14px 18px; 
            border-radius: 12px; 
            margin-bottom: 20px; 
            text-align: center; 
            border-left: 4px solid #c0392b;
            font-size: 14px;
        }
        
        .auth-box .remember { 
            display: flex; 
            justify-content: space-between; 
            margin-top: 14px; 
            align-items: center; 
            font-size: 14px;
        }
        .auth-box .remember a { 
            color: #b45f2b; 
            text-decoration: none; 
            font-size: 14px; 
        }
        .auth-box .remember a:hover { 
            text-decoration: underline; 
        }
        .auth-box .remember label { 
            margin-top: 0; 
            font-weight: 400; 
            color: #666; 
            display: flex; 
            align-items: center; 
            gap: 8px; 
        }
        .auth-box .remember input[type="checkbox"] { 
            width: 18px; 
            height: 18px; 
            margin-top: 0; 
        }
        
        .auth-footer { 
            text-align: center; 
            margin-top: 25px; 
            padding-top: 20px; 
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
        
        @media (max-width: 480px) { 
            .auth-box { 
                padding: 30px 20px; 
            } 
        }
    </style>
</head>
<body>

<div class="auth-box">
    <div class="logo">
        <div class="logo-icon">S</div>
        <h2>SEN'Q <span>Restaurant</span></h2>
        <p>Welcome back! Please login to your account</p>
    </div>
    
    <?php if($error): ?>
        <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter your email" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter your password" required>

        <div class="remember">
            <label><input type="checkbox" name="remember"> Remember me</label>
            <a href="#">Forgot password?</a>
        </div>

        <button type="submit" class="btn"><i class="fas fa-sign-in-alt"></i> Login</button>

        <div class="links">
            Don't have an account? <a href="signup.php">Sign up now</a>
        </div>
        <div class="auth-footer">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
    </form>
</div>

</body>
</html>
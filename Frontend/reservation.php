<?php
session_start();
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

$error = '';
$success = '';

// Get user info if logged in
$user_name = '';
$user_email = '';
$user_phone = '';

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user) {
        $user_name = $user['full_name'];
        $user_email = $user['email'];
        $user_phone = $user['phone'] ?? '';
    }
}

// Handle reservation submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $reservation_date = $_POST['reservation_date'] ?? '';
    $reservation_time = $_POST['reservation_time'] ?? '';
    $guests = $_POST['guests'] ?? 1;
    $table_type = $_POST['table_type'] ?? '';
    $occasion = $_POST['occasion'] ?? '';
    $special_requests = $_POST['special_requests'] ?? '';
    
    if (empty($full_name) || empty($email) || empty($phone) || empty($reservation_date) || empty($reservation_time)) {
        $error = 'All required fields must be filled';
    } else {
        $name_parts = explode(' ', $full_name, 2);
        $fname = $name_parts[0];
        $lname = $name_parts[1] ?? '';
        
        // Insert into reservation table
        $sql = "INSERT INTO reservation (fname, lname, guest, email, phone, date_res, time, suggestions) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssss", $fname, $lname, $guests, $email, $phone, $reservation_date, $reservation_time, $special_requests);
        
        if ($stmt->execute()) {
            // Also insert into tablebook for admin
            $sql2 = "INSERT INTO tablebook (Title, FName, LName, Email, Phone, Tbltyp, time, date, status) 
                     VALUES ('', ?, ?, ?, ?, ?, ?, ?, 'NOT CONFIRM')";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("sssssss", $fname, $lname, $email, $phone, $table_type, $reservation_time, $reservation_date);
            $stmt2->execute();
            
            $success = 'Reservation confirmed! We will contact you shortly.';
        } else {
            $error = 'Reservation failed. Please try again.';
        }
    }
}

// Get user's reservations
$user_reservations = [];
if (isset($_SESSION['user_id'])) {
    $result = $conn->query("SELECT * FROM reservation WHERE email = '" . $_SESSION['user_email'] . "' ORDER BY date_res DESC");
    while ($row = $result->fetch_assoc()) {
        $user_reservations[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation - SEN'Q Restaurant</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;400;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4a085e9b53.js" crossorigin="anonymous"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #fefcf3; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .btn { display: inline-block; background: #b45f2b; color: #fff; padding: 12px 30px; border-radius: 40px; font-weight: 600; border: none; cursor: pointer; transition: 0.3s; font-size: 1rem; text-decoration: none; }
        .btn:hover { background: #8a471f; transform: scale(1.03); }
        .btn-outline { background: transparent; border: 2px solid #b45f2b; color: #b45f2b; }
        .btn-outline:hover { background: #b45f2b; color: #fff; }
        .btn-danger { background: #e74c3c; color: #fff; }
        .btn-danger:hover { background: #c0392b; }
        
        .top-bar { background: #1a1a1a; color: #eee; padding: 8px 0; }
        .top-bar .flex { display: flex; justify-content: flex-end; gap: 15px; align-items: center; flex-wrap: wrap; }
        .top-bar a { color: #ddd; font-weight: 500; font-size: 0.95rem; text-decoration: none; }
        .top-bar a:hover { color: #f1c40f; }
        .top-bar .welcome { color: #f1c40f; margin-right: 10px; }
        
        .main-header { background: #2c2c2c; padding: 12px 0; border-bottom: 3px solid #b45f2b; }
        .main-header .flex { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .logo-area { display: flex; align-items: center; gap: 12px; }
        .logo-icon { background: #b45f2b; color: #fff; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; }
        .logo-area h1 { font-size: 2rem; color: #f1c40f; letter-spacing: 2px; }
        .nav-links { display: flex; gap: 28px; flex-wrap: wrap; }
        .nav-links a { color: #f0f0f0; font-weight: 500; font-size: 1.05rem; padding: 6px 0; border-bottom: 2px solid transparent; transition: 0.2s; text-decoration: none; }
        .nav-links a:hover, .nav-links a.active { border-bottom-color: #f1c40f; color: #f1c40f; }
        
        .footer { background: #1e1a16; color: #d6cec4; padding: 50px 0 20px; margin-top: 40px; }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 30px; }
        .footer-grid h4 { color: #f1c40f; margin-bottom: 16px; font-size: 1.2rem; }
        .footer-grid ul { list-style: none; }
        .footer-grid ul li { margin-bottom: 8px; }
        .footer-grid ul li a { color: #d6cec4; text-decoration: none; }
        .footer-grid ul li a:hover { color: #f1c40f; }
        .footer-bottom { border-top: 1px solid #3d322a; margin-top: 40px; padding-top: 20px; text-align: center; font-size: 0.9rem; color: #a3978a; }
        .social-icons { display: flex; gap: 12px; font-size: 1.8rem; }
        .social-icons a { color: #d6cec4; transition: 0.3s; }
        .social-icons a:hover { color: #f1c40f; }
        
        .section-title { text-align: center; font-size: 2.4rem; font-weight: 600; margin: 50px 0 30px; color: #2c1f16; position: relative; }
        .section-title:after { content: ''; display: block; width: 80px; height: 4px; background: #b45f2b; margin: 10px auto 0; }
        
        .reservation-page { padding: 40px 0; }
        .reservation-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start; }
        .reservation-info { background: #2c1f16; color: #fff; padding: 40px; border-radius: 20px; }
        .reservation-info h3 { color: #f1c40f; margin-bottom: 20px; font-size: 1.8rem; }
        .reservation-info ul { list-style: none; }
        .reservation-info ul li { padding: 10px 0; border-bottom: 1px solid #3d322a; }
        .reservation-info ul li:last-child { border-bottom: none; }
        .reservation-form { background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 6px 18px rgba(0,0,0,0.08); }
        .reservation-form label { display: block; font-weight: 600; margin-top: 12px; }
        .reservation-form input, .reservation-form select, .reservation-form textarea { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; margin-top: 4px; font-size: 15px; transition: 0.3s; }
        .reservation-form input:focus, .reservation-form select:focus, .reservation-form textarea:focus { border-color: #b45f2b; outline: none; box-shadow: 0 0 0 3px rgba(180,95,43,0.1); }
        .reservation-form .btn { width: 100%; margin-top: 20px; padding: 14px; }
        .reservation-form .error { background: #fee; color: #c00; padding: 12px; border-radius: 10px; margin-bottom: 15px; text-align: center; border-left: 4px solid #c00; }
        .reservation-form .success { background: #efe; color: #060; padding: 12px; border-radius: 10px; margin-bottom: 15px; text-align: center; border-left: 4px solid #060; }
        
        .my-reservations { margin-top: 40px; }
        .reservation-card { background: #fff; border-radius: 15px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; border-left: 4px solid #b45f2b; }
        .reservation-card .info { flex: 1; }
        .reservation-card .info h4 { color: #2c1f16; }
        .reservation-card .info p { color: #666; font-size: 0.9rem; }
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-upcoming { background: #d4edda; color: #155724; }
        .badge-past { background: #f8d7da; color: #721c24; }
        .badge-today { background: #fff3cd; color: #856404; }
        
        @media (max-width: 800px) { 
            .reservation-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="container">
        <div class="flex">
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="welcome"><i class="fas fa-user-circle"></i> Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Log in</a>
                <a href="signup.php"><i class="fas fa-user-plus"></i> Sign up</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<header class="main-header">
    <div class="container">
        <div class="flex">
            <div class="logo-area">
                <div class="logo-icon">S</div>
                <h1>SEN'Q</h1>
            </div>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="menu.php">Menu</a>
                <a href="booking.php">Book Table</a>
                <a href="reservation.php" class="active">Reservation</a>
                <a href="order.php">Order</a>
                <a href="contact.php">Contact</a>
            </nav>
        </div>
    </div>
</header>

<section class="reservation-page">
    <div class="container">
        <h2 class="section-title">Make a Reservation</h2>
        <div class="reservation-grid">
            <div class="reservation-info">
                <h3><i class="fas fa-clock"></i> Working Hours</h3>
                <ul>
                    <li><strong>Breakfast:</strong> 7:00 AM – 11:30 AM</li>
                    <li><strong>Lunch:</strong> 11:45 AM – 6:45 PM</li>
                    <li><strong>Dinner:</strong> 7:00 PM – 10:30 PM</li>
                </ul>
                <div style="margin-top:30px;">
                    <h3 style="color:#f1c40f;"><i class="fas fa-info-circle"></i> Table Types</h3>
                    <ul>
                        <li>Messob (Traditional)</li>
                        <li>Circular Table</li>
                        <li>Rectangular Table</li>
                    </ul>
                </div>
                <div style="margin-top:30px;">
                    <h3 style="color:#f1c40f;"><i class="fas fa-phone"></i> Contact</h3>
                    <ul>
                        <li>+251 924 950 125</li>
                        <li>Addis Ababa, Kasaches</li>
                    </ul>
                </div>
                <div style="margin-top:30px;">
                    <h3 style="color:#f1c40f;"><i class="fas fa-info-circle"></i> Reservation Policy</h3>
                    <ul>
                        <li>Please arrive 15 minutes before your reservation</li>
                        <li>We hold tables for 30 minutes</li>
                        <li>For parties over 10, please call us directly</li>
                    </ul>
                </div>
            </div>

            <div class="reservation-form">
                <h3 style="margin-bottom:20px;">Reservation Form</h3>
                
                <?php if($error): ?>
                    <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <label>Full Name *</label>
                    <input type="text" name="full_name" placeholder="Enter your full name" required value="<?php echo htmlspecialchars($user_name); ?>">

                    <label>Email *</label>
                    <input type="email" name="email" placeholder="Enter your email" required value="<?php echo htmlspecialchars($user_email); ?>">

                    <label>Phone Number *</label>
                    <input type="tel" name="phone" placeholder="Enter phone number" required value="<?php echo htmlspecialchars($user_phone); ?>">

                    <label>Date *</label>
                    <input type="date" name="reservation_date" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>">

                    <label>Time *</label>
                    <input type="time" name="reservation_time" required value="19:00">

                    <label>Number of Guests *</label>
                    <select name="guests" required>
                        <?php for($i = 1; $i <= 15; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>

                    <label>Table Type</label>
                    <select name="table_type">
                        <option value="Messob">Messob (Traditional)</option>
                        <option value="Circular">Circular Table</option>
                        <option value="Rectangular">Rectangular Table</option>
                    </select>

                    <label>Occasion</label>
                    <select name="occasion">
                        <option value="">Select Occasion</option>
                        <option value="birthday">Birthday</option>
                        <option value="anniversary">Anniversary</option>
                        <option value="celebration">Celebration</option>
                        <option value="business">Business Dinner</option>
                        <option value="casual">Casual Dining</option>
                        <option value="other">Other</option>
                    </select>

                    <label>Special Requests</label>
                    <textarea name="special_requests" placeholder="Any special requests or notes..." rows="3"></textarea>

                    <button type="submit" class="btn"><i class="fas fa-calendar-check"></i> Make Reservation</button>
                </form>
            </div>
        </div>

        <?php if(isset($_SESSION['user_id']) && !empty($user_reservations)): ?>
        <div class="my-reservations">
            <h2 class="section-title">My Reservations</h2>
            <?php foreach($user_reservations as $res): 
                $today = date('Y-m-d');
                $statusClass = $res['date_res'] == $today ? 'badge-today' : ($res['date_res'] > $today ? 'badge-upcoming' : 'badge-past');
                $statusText = $res['date_res'] == $today ? 'Today' : ($res['date_res'] > $today ? 'Upcoming' : 'Past');
            ?>
            <div class="reservation-card">
                <div class="info">
                    <h4><?php echo $res['fname'] . ' ' . $res['lname']; ?></h4>
                    <p>
                        <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($res['date_res'])); ?>
                        &nbsp;|&nbsp; <i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($res['time'])); ?>
                        &nbsp;|&nbsp; <i class="fas fa-users"></i> <?php echo $res['guest']; ?> guests
                    </p>
                </div>
                <div>
                    <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <h4>Company</h4>
                <ul>
                    <li><a href="about.php">About us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="menu.php">Menu</a></li>
                    <li><a href="order.php">Order</a></li>
                </ul>
            </div>
           
            <div>
                <h4>Contact</h4>
                <ul>
                    <li><i class="fas fa-phone"></i> +251924950125</li>
                    <li><i class="fas fa-map-marker-alt"></i> Addis Ababa</li>
                </ul>
            </div>
            <div>
                <h4>Follow</h4>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-telegram"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?php echo date('Y'); ?> SEN'Q Restaurant. All rights reserved.
        </div>
    </div>
</footer>

</body>
</html>
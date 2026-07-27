<?php
// ============================================================
// NO session_start() HERE - IT'S ALREADY IN HEADER.PHP
// ============================================================

require_once 'includes/header.php';
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

$error = '';
$success = '';

// Ensure the reservation table has the correct columns
$conn->query("CREATE TABLE IF NOT EXISTS reservation (
    reserve_id INT PRIMARY KEY AUTO_INCREMENT,
    fname VARCHAR(50) NOT NULL,
    lname VARCHAR(50) NOT NULL,
    guest INT NOT NULL,
    table_type VARCHAR(50),
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    date_res DATE NOT NULL,
    time TIME NOT NULL,
    suggestions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

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
    $special_requests = $_POST['special_requests'] ?? '';
    
    if (empty($full_name) || empty($email) || empty($phone) || empty($reservation_date) || empty($reservation_time)) {
        $error = 'All required fields must be filled';
    } else {
        $name_parts = explode(' ', $full_name, 2);
        $fname = $name_parts[0];
        $lname = $name_parts[1] ?? '';
        
        $sql = "INSERT INTO reservation (fname, lname, guest, table_type, email, phone, date_res, time, suggestions) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssissssss", $fname, $lname, $guests, $table_type, $email, $phone, $reservation_date, $reservation_time, $special_requests);
        
        if ($stmt->execute()) {
            $success = 'Reservation confirmed! We will contact you shortly.';
        } else {
            $error = 'Reservation failed. Please try again.';
        }
    }
}
?>

<style>
    .reservation-page { padding: 40px 0; }
    .reservation-page h2 { 
        text-align: center; 
        font-size: 2.4rem; 
        color: #2c1f16; 
        margin-bottom: 10px; 
        position: relative;
    }
    .reservation-page h2:after {
        content: '';
        display: block;
        width: 80px;
        height: 4px;
        background: #b45f2b;
        margin: 10px auto 0;
    }
    .reservation-page .subtitle { 
        text-align: center; 
        color: #666; 
        margin-bottom: 40px; 
        font-size: 1rem;
    }
    .reservation-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start; }
    .reservation-info { background: #2c1f16; color: #fff; padding: 40px; border-radius: 20px; }
    .reservation-info h3 { color: #f1c40f; margin-bottom: 20px; font-size: 1.8rem; }
    .reservation-info ul { list-style: none; }
    .reservation-info ul li { padding: 10px 0; border-bottom: 1px solid #3d322a; }
    .reservation-form { background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 6px 18px rgba(0,0,0,0.08); }
    .reservation-form label { display: block; font-weight: 600; margin-top: 12px; color: #2c1f16; }
    .reservation-form label .required { color: #e74c3c; }
    .reservation-form input, .reservation-form select, .reservation-form textarea { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; margin-top: 4px; font-size: 15px; transition: 0.3s; background: #faf8f5; }
    .reservation-form input:focus, .reservation-form select:focus, .reservation-form textarea:focus { border-color: #b45f2b; outline: none; box-shadow: 0 0 0 3px rgba(180,95,43,0.08); background: #fff; }
    .reservation-form .btn { width: 100%; margin-top: 20px; padding: 14px; border: none; border-radius: 12px; background: linear-gradient(135deg, #b45f2b, #8a471f); color: #fff; font-weight: 600; font-size: 16px; cursor: pointer; transition: 0.3s; }
    .reservation-form .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(180,95,43,0.25); }
    .reservation-form .error { background: #fdf0ed; color: #c0392b; padding: 12px 16px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #c0392b; font-size: 14px; }
    .reservation-form .success { background: #edf7ed; color: #1e8449; padding: 12px 16px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #1e8449; font-size: 14px; }
    .my-reservations { margin-top: 40px; }
    .reservation-card { background: #fff; border-radius: 15px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; border-left: 4px solid #b45f2b; }
    .reservation-card .info { flex: 1; }
    .reservation-card .info h4 { color: #2c1f16; }
    .reservation-card .info p { color: #666; font-size: 0.9rem; }
    .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-upcoming { background: #d4edda; color: #155724; }
    .badge-past { background: #f8d7da; color: #721c24; }
    .badge-today { background: #fff3cd; color: #856404; }
    @media (max-width: 800px) { .reservation-grid { grid-template-columns: 1fr; } }
</style>

<section class="reservation-page">
    <div class="container">
        <h2>Make a Reservation</h2>
        <p class="subtitle">Reserve your table for an authentic Ethiopian dining experience</p>
        
        <?php if($error): ?>
            <div class="error" style="background:#fdf0ed;color:#c0392b;padding:12px 16px;border-radius:10px;margin-bottom:15px;border-left:4px solid #c0392b;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="success" style="background:#edf7ed;color:#1e8449;padding:12px 16px;border-radius:10px;margin-bottom:15px;border-left:4px solid #1e8449;">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

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
                    <h3 style="color:#f1c40f;"><i class="fas fa-info-circle"></i> Policy</h3>
                    <ul>
                        <li>Please arrive 15 minutes before your reservation</li>
                        <li>We hold tables for 30 minutes</li>
                        <li>For parties over 10, please call us directly</li>
                    </ul>
                </div>
            </div>

            <div class="reservation-form">
                <h3 style="margin-bottom:20px;color:#2c1f16;">Reservation Form</h3>

                <form method="POST" action="">
                    <label>Full Name <span class="required">*</span></label>
                    <input type="text" name="full_name" placeholder="Enter your full name" required value="<?php echo htmlspecialchars($user_name); ?>">

                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" placeholder="Enter your email" required value="<?php echo htmlspecialchars($user_email); ?>">

                    <label>Phone Number <span class="required">*</span></label>
                    <input type="tel" name="phone" placeholder="Enter your phone number" required value="<?php echo htmlspecialchars($user_phone); ?>">

                    <label>Date <span class="required">*</span></label>
                    <input type="date" name="reservation_date" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>">

                    <label>Time <span class="required">*</span></label>
                    <input type="time" name="reservation_time" required value="19:00">

                    <label>Number of Guests <span class="required">*</span></label>
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

                    <label>Special Requests</label>
                    <textarea name="special_requests" placeholder="Any special requests or notes..." rows="3"></textarea>

                    <button type="submit" class="btn"><i class="fas fa-calendar-check"></i> Book Now</button>
                </form>
            </div>
        </div>

        <?php if(isset($_SESSION['user_id'])): 
            $user_reservations = $conn->query("SELECT * FROM reservation WHERE email = '" . $_SESSION['user_email'] . "' ORDER BY date_res DESC");
            if($user_reservations->num_rows > 0):
        ?>
        <div class="my-reservations">
            <h2 style="text-align:center;font-size:2rem;color:#2c1f16;margin-bottom:30px;position:relative;">My Reservations</h2>
            <?php while($res = $user_reservations->fetch_assoc()): 
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
                        <?php if($res['table_type']): ?>
                            &nbsp;|&nbsp; <i class="fas fa-table"></i> <?php echo $res['table_type']; ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
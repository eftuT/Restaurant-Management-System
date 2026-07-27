<?php
require_once 'includes/header.php';
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $booking_date = $_POST['booking_date'] ?? '';
    $booking_time = $_POST['booking_time'] ?? '';
    $guests = $_POST['guests'] ?? 1;
    $table_type = $_POST['table_type'] ?? '';
    $requests = $_POST['requests'] ?? '';
    
    if(empty($full_name) || empty($email) || empty($phone) || empty($booking_date) || empty($booking_time)) {
        $error = 'All required fields must be filled';
    } else {
        $conn->query("CREATE TABLE IF NOT EXISTS reservation (
            reserve_id INT PRIMARY KEY AUTO_INCREMENT,
            fname VARCHAR(50) NOT NULL,
            lname VARCHAR(50) NOT NULL,
            guest INT NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            date_res DATE NOT NULL,
            time TIME NOT NULL,
            suggestions TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $name_parts = explode(' ', $full_name, 2);
        $fname = $name_parts[0];
        $lname = $name_parts[1] ?? '';
        
        $sql = "INSERT INTO reservation (fname, lname, guest, email, phone, date_res, time, suggestions) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssss", $fname, $lname, $guests, $email, $phone, $booking_date, $booking_time, $requests);
        
        if($stmt->execute()) {
            $success = 'Booking confirmed! We will contact you shortly.';
        } else {
            $error = 'Booking failed. Please try again.';
        }
    }
}
?>

<style>
    .booking-page { padding: 40px 0; }
    .booking-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start; }
    .booking-info { background: #2c1f16; color: #fff; padding: 40px; border-radius: 20px; }
    .booking-info h3 { color: #f1c40f; margin-bottom: 20px; font-size: 1.8rem; }
    .booking-info ul { list-style: none; }
    .booking-info ul li { padding: 10px 0; border-bottom: 1px solid #3d322a; }
    .booking-info ul li:last-child { border-bottom: none; }
    .booking-form { background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 6px 18px rgba(0,0,0,0.08); }
    .booking-form label { display: block; font-weight: 600; margin-top: 12px; }
    .booking-form input, .booking-form select, .booking-form textarea { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; margin-top: 4px; font-size: 15px; transition: 0.3s; }
    .booking-form input:focus, .booking-form select:focus, .booking-form textarea:focus { border-color: #b45f2b; outline: none; box-shadow: 0 0 0 3px rgba(180,95,43,0.1); }
    .booking-form .btn { width: 100%; margin-top: 20px; padding: 14px; }
    .booking-form .error { background: #fee; color: #c00; padding: 12px; border-radius: 10px; margin-bottom: 15px; text-align: center; border-left: 4px solid #c00; }
    .booking-form .success { background: #efe; color: #060; padding: 12px; border-radius: 10px; margin-bottom: 15px; text-align: center; border-left: 4px solid #060; }
    @media (max-width: 800px) { .booking-grid { grid-template-columns: 1fr; } }
</style>

<section class="booking-page">
    <div class="container">
        <h2 class="section-title">Book a Table</h2>
        <div class="booking-grid">
            <div class="booking-info">
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
                    <p><i class="fas fa-phone" style="color:#f1c40f;"></i> +251 924 950 125</p>
                    <p><i class="fas fa-map-marker-alt" style="color:#f1c40f;"></i> Addis Ababa, Kasaches</p>
                </div>
            </div>

            <div class="booking-form">
                <h3 style="margin-bottom:20px;">Reservation Form</h3>
                
                <?php if($error): ?>
                    <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <label>Full name *</label>
                    <input type="text" name="full_name" placeholder="Enter your name" required value="<?php echo $_SESSION['user_name'] ?? ''; ?>">

                    <label>Email *</label>
                    <input type="email" name="email" placeholder="Enter your email" required value="<?php echo $_SESSION['user_email'] ?? ''; ?>">

                    <label>Phone number *</label>
                    <input type="tel" name="phone" placeholder="Enter phone number" required>

                    <label>Date *</label>
                    <input type="date" name="booking_date" required min="<?php echo date('Y-m-d'); ?>">

                    <label>Time *</label>
                    <input type="time" name="booking_time" required>

                    <label>Number of guests *</label>
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

                    <label>Special requests</label>
                    <textarea name="requests" placeholder="Any special requests..." rows="3"></textarea>

                    <button type="submit" class="btn"><i class="fas fa-calendar-check"></i> Book Now</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
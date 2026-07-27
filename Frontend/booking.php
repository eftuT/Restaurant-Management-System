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
            table_type VARCHAR(50),
            suggestions TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $name_parts = explode(' ', $full_name, 2);
        $fname = $name_parts[0];
        $lname = $name_parts[1] ?? '';
        
        $sql = "INSERT INTO reservation (fname, lname, guest, email, phone, date_res, time, table_type, suggestions) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssissssss", $fname, $lname, $guests, $email, $phone, $booking_date, $booking_time, $table_type, $requests);
        
        if($stmt->execute()) {
            $success = 'Booking confirmed! We will contact you shortly.';
        } else {
            $error = 'Booking failed. Please try again.';
        }
    }
}
?>

<style>
    .booking-page { 
        padding: 20px 0 40px 0;  /* Reduced top padding from 40px to 20px */
    }
    .booking-page .section-title {
        margin-top: 0;  /* Remove top margin */
        margin-bottom: 30px;
    }
    .booking-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start; }
    .booking-info { background: #2c1f16; color: #fff; padding: 40px; border-radius: 20px; }
    .booking-info h3 { color: #f1c40f; margin-bottom: 20px; font-size: 1.8rem; }
    .booking-info ul { list-style: none; padding: 0; }
    .booking-info ul li { padding: 10px 0; border-bottom: 1px solid #3d322a; }
    .booking-info ul li:last-child { border-bottom: none; }
    .booking-form { background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 6px 18px rgba(0,0,0,0.08); }
    .booking-form h3 { margin-bottom: 25px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .form-group { margin-bottom: 15px; }
    .form-group.full-width { grid-column: 1 / -1; }
    .booking-form label { display: block; font-weight: 600; margin-bottom: 5px; font-size: 14px; }
    .booking-form label .required { color: #e74c3c; }
    .booking-form input, 
    .booking-form select, 
    .booking-form textarea { 
        width: 100%; 
        padding: 10px 14px; 
        border: 2px solid #e0e0e0; 
        border-radius: 8px; 
        font-size: 14px; 
        transition: 0.3s; 
        background: #fafafa;
    }
    .booking-form input:focus, 
    .booking-form select:focus, 
    .booking-form textarea:focus { 
        border-color: #b45f2b; 
        outline: none; 
        box-shadow: 0 0 0 3px rgba(180,95,43,0.1);
        background: #fff;
    }
    .booking-form textarea { resize: vertical; min-height: 80px; }
    .booking-form .btn { 
        width: 100%; 
        margin-top: 5px; 
        padding: 14px; 
        font-size: 16px;
        background: #b45f2b;
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.3s;
    }
    .booking-form .btn:hover { 
        background: #8e4a22; 
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(180,95,43,0.3);
    }
    .booking-form .btn i { margin-right: 8px; }
    .booking-form .error { 
        background: #fee; 
        color: #c00; 
        padding: 12px 16px; 
        border-radius: 8px; 
        margin-bottom: 20px; 
        border-left: 4px solid #c00; 
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .booking-form .success { 
        background: #efe; 
        color: #060; 
        padding: 12px 16px; 
        border-radius: 8px; 
        margin-bottom: 20px; 
        border-left: 4px solid #060;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    @media (max-width: 900px) { 
        .booking-grid { grid-template-columns: 1fr; }
        .form-row { grid-template-columns: 1fr; }
        .form-group.full-width { grid-column: 1; }
    }
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
                <h3>Reservation Form</h3>
                
                <?php if($error): ?>
                    <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full name <span class="required">*</span></label>
                            <input type="text" name="full_name" placeholder="Enter your name" required value="<?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" placeholder="Enter your email" required value="<?php echo isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>Phone number <span class="required">*</span></label>
                            <input type="tel" name="phone" placeholder="Enter phone number" required>
                        </div>

                        <div class="form-group">
                            <label>Guests <span class="required">*</span></label>
                            <select name="guests" required>
                                <?php for($i = 1; $i <= 15; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $i == 1 ? 'Guest' : 'Guests'; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Date <span class="required">*</span></label>
                            <input type="date" name="booking_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="form-group">
                            <label>Time <span class="required">*</span></label>
                            <input type="time" name="booking_time" required>
                        </div>

                        <div class="form-group full-width">
                            <label>Table Type</label>
                            <select name="table_type">
                                <option value="">Select table type</option>
                                <option value="Messob">Messob (Traditional)</option>
                                <option value="Circular">Circular Table</option>
                                <option value="Rectangular">Rectangular Table</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label>Special requests</label>
                            <textarea name="requests" placeholder="Any special requests..." rows="3"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn"><i class="fas fa-calendar-check"></i> Book Now</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
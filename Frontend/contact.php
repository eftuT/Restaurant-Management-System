<?php
// NO session_start() here - it's in header.php
require_once 'includes/header.php';
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if(empty($name) || empty($email) || empty($message)) {
        $error = 'Name, email and message are required';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {
        $conn->query("CREATE TABLE IF NOT EXISTS contact (
            id INT PRIMARY KEY AUTO_INCREMENT,
            fullname VARCHAR(100) NOT NULL,
            phoneno VARCHAR(20),
            email VARCHAR(100) NOT NULL,
            approval VARCHAR(20) DEFAULT 'Not Allowed'
        )");
        
        $stmt = $conn->prepare("INSERT INTO contact (fullname, phoneno, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $phone, $email);
        if($stmt->execute()) {
            $success = 'Message sent successfully! We will get back to you soon.';
        } else {
            $error = 'Failed to send message. Please try again.';
        }
    }
}
?>

<style>
    .contact-page { padding: 40px 0; }
    .contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; background: #f7f2e9; padding: 50px 40px; border-radius: 30px; }
    .contact-info h3 { font-size: 1.8rem; color: #2c1f16; margin-bottom: 20px; }
    .contact-info p { padding: 8px 0; font-size: 1.1rem; }
    .contact-info i { width: 30px; color: #b45f2b; }
    .contact-form input, .contact-form textarea { width: 100%; padding: 14px 18px; border: 2px solid #ddd; border-radius: 10px; font-size: 1rem; margin-bottom: 18px; background: #fff; transition: 0.3s; }
    .contact-form input:focus, .contact-form textarea:focus { border-color: #b45f2b; outline: none; box-shadow: 0 0 0 3px rgba(180,95,43,0.1); }
    .contact-form textarea { border-radius: 10px; min-height: 120px; resize: vertical; }
    .contact-form .btn { width: 100%; padding: 14px; }
    .contact-form .error { background: #fee; color: #c00; padding: 12px; border-radius: 10px; margin-bottom: 15px; text-align: center; border-left: 4px solid #c00; }
    .contact-form .success { background: #efe; color: #060; padding: 12px; border-radius: 10px; margin-bottom: 15px; text-align: center; border-left: 4px solid #060; }
    @media (max-width: 800px) { .contact-grid { grid-template-columns: 1fr; padding: 30px 20px; } }
</style>

<section class="contact-page">
    <div class="container">
        <h2 class="section-title">Contact Us</h2>
        <div class="contact-grid">
            <div class="contact-info">
                <h3>Get in touch</h3>
                <p><i class="fas fa-phone-alt"></i> +251 924 950 125</p>
                <p><i class="fas fa-phone-alt"></i> +251 924 950 125</p>
                <p><i class="fas fa-map-marker-alt"></i> Ethiopia, Addis Ababa, Kasaches</p>
                <p><i class="fas fa-clock"></i> Mon–Sun: 7:00 AM – 9:00 PM</p>
                
                <div style="margin-top:30px;">
                    <h4 style="color:#2c1f16;margin-bottom:10px;">Follow us</h4>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-telegram"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <div style="margin-top:30px;">
                    <h4 style="color:#2c1f16;margin-bottom:10px;">Owners</h4>
                    <ul style="list-style:none;">
                        <li>Dibora</li>
                        <li>Edelawit</li>
                        <li>Eden</li>
                        <li>Eftu</li>
                        <li>Hawi</li>
                    </ul>
                </div>
            </div>

            <div class="contact-form">
                <?php if($error): ?>
                    <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <input type="text" name="name" placeholder="Your name" required>
                    <input type="email" name="email" placeholder="Your email" required>
                    <input type="tel" name="phone" placeholder="Your phone">
                    <textarea name="message" placeholder="Message..." rows="5" required></textarea>
                    <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Send message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
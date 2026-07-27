<?php
// NO session_start() here - it's in header.php
require_once 'includes/header.php';
require_once 'C:/xampp/htdocs/Restaurant-Management-System/Backend/includes/db.php';
?>

<style>
    .about-page { padding: 40px 0; }
    .about-hero { background: linear-gradient(135deg, #f7f2e9 0%, #fefcf3 50%, #f5ede3 100%); padding: 60px 20px; text-align: center; border-radius: 20px; margin-bottom: 40px; }
    .about-hero h1 { font-size: 3rem; color: #2c1f16; margin-bottom: 10px; }
    .about-hero h1 span { color: #b45f2b; }
    .about-hero p { font-size: 1.2rem; max-width: 700px; margin: 0 auto; color: #666; }
    .about-content { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; }
    .about-content .text h2 { font-size: 2rem; color: #2c1f16; margin-bottom: 20px; }
    .about-content .text p { color: #555; margin-bottom: 15px; line-height: 1.8; }
    .about-content .image { background: #e6dccc; height: 400px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 80px; color: #b45f2b; }
    .values-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-top: 40px; }
    .value-card { background: #fff; padding: 30px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); text-align: center; border: 1px solid #eee; transition: 0.3s; }
    .value-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
    .value-card i { font-size: 3rem; color: #b45f2b; margin-bottom: 15px; }
    .value-card h4 { font-size: 1.2rem; color: #2c1f16; margin-bottom: 10px; }
    .value-card p { color: #666; font-size: 0.95rem; }
    .team-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; margin-top: 40px; }
    .team-card { background: #fff; padding: 25px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); text-align: center; border: 1px solid #eee; }
    .team-card .avatar { width: 100px; height: 100px; border-radius: 50%; background: #b45f2b; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #fff; font-weight: bold; }
    .team-card h4 { color: #2c1f16; }
    .team-card p { color: #999; font-size: 0.85rem; }
    @media (max-width: 800px) { .about-content { grid-template-columns: 1fr; } .about-hero h1 { font-size: 2rem; } }
</style>

<section class="about-page">
    <div class="container">
        <div class="about-hero">
            <h1>About <span>SEN'Q</span></h1>
            <p>Discover the story behind our traditional Ethiopian restaurant</p>
        </div>

        <div class="about-content">
            <div class="text">
                <h2>Our Story</h2>
                <p>The word <strong>SEN'Q</strong> is a collection of food put in a special container called <strong>AGELGIL</strong>. It is mainly practiced in the rural areas of Ethiopia to deliver food for farmers specifically who work in a team for crop gathering.</p>
                <p>At lunchtime, their wives come with food inside Agelgil which is called <strong>SEN'Q</strong> and also with a traditional drink called Tela. Not only for farmers, it is also used for a person who will travel long way by foot. When he gets tired, he will rest and eat his SEN'Q whose mother or wife made it.</p>
                <p>We bring that warmth to your table, every day. Our restaurant is dedicated to preserving this beautiful Ethiopian tradition and sharing it with the world.</p>
                <a href="menu.php" class="btn" style="margin-top:20px;">Explore Our Menu</a>
            </div>
            <div class="image">
                <i class="fas fa-utensils"></i>
            </div>
        </div>

        <h2 class="section-title">Our Values</h2>
        <div class="values-grid">
            <div class="value-card">
                <i class="fas fa-heart"></i>
                <h4>Authenticity</h4>
                <p>We stay true to traditional Ethiopian recipes and cooking methods.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-users"></i>
                <h4>Community</h4>
                <p>We believe in bringing people together through food and shared experiences.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-leaf"></i>
                <h4>Quality</h4>
                <p>We use only the freshest ingredients to ensure the best taste.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-smile"></i>
                <h4>Hospitality</h4>
                <p>We treat every guest like family with warm Ethiopian hospitality.</p>
            </div>
        </div>

        <h2 class="section-title">Meet Our Team</h2>
        <div class="team-grid">
            <div class="team-card">
                <div class="avatar">D</div>
                <h4>Dibora</h4>
                <p>Head Chef</p>
            </div>
            <div class="team-card">
                <div class="avatar">E</div>
                <h4>Edelawit</h4>
                <p>Manager</p>
            </div>
            <div class="team-card">
                <div class="avatar">E</div>
                <h4>Eden</h4>
                <p>Lead Server</p>
            </div>
            <div class="team-card">
                <div class="avatar">E</div>
                <h4>Eftu</h4>
                <p>Chef</p>
            </div>
            <div class="team-card">
                <div class="avatar">H</div>
                <h4>Hawi</h4>
                <p>Marketing</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
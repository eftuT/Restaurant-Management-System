<?php
ob_start();
require 'credentials.php';
$pid = $_GET['sid'];
$conn = mysqli_connect($host, $user, $password);
mysqli_select_db($conn, $database);
$query = "SELECT * FROM tablebook where id = '$pid'";
$rs = mysqli_query($conn, $query);
$row = mysqli_fetch_array($rs);
$id = $row["id"];
$title = $row["Title"];
$fname = $row["FName"];
$lname = $row["LName"];
$email = $row["Email"];
$national = $row["National"];
$country = $row["Country"];
$phone = $row["Phone"];
$tbltyp = $row["Tbltyp"];
$purpose = $row["Purpose"];
$meal = $row["Meal"];
$tme = $row["time"];
$dte = $row["date"];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Details of Booking</title>
    <style>
        * { border: 0; box-sizing: content-box; color: inherit; font-family: inherit; font-size: inherit; font-style: inherit; font-weight: inherit; line-height: inherit; list-style: none; margin: 0; padding: 0; text-decoration: none; vertical-align: top; }
        *[contenteditable] { border-radius: 0.25em; min-width: 1em; outline: 0; cursor: pointer; }
        *[contenteditable]:hover, *[contenteditable]:focus { background: #DEF; box-shadow: 0 0 1em 0.5em #DEF; }
        h1 { font: bold 100% sans-serif; letter-spacing: 0.5em; text-align: center; text-transform: uppercase; }
        table { font-size: 75%; table-layout: fixed; width: 100%; border-collapse: separate; border-spacing: 2px; }
        th, td { border-width: 1px; padding: 0.5em; position: relative; text-align: left; border-radius: 0.25em; border-style: solid; }
        th { background: #EEE; border-color: #BBB; }
        td { border-color: #DDD; }
        html { font: 16px/1 'Open Sans', sans-serif; overflow: auto; padding: 0.5in; background: #999; cursor: default; }
        body { box-sizing: border-box; height: 11in; margin: 0 auto; overflow: hidden; padding: 0.5in; width: 8.5in; background: #FFF; border-radius: 1px; box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5); }
        header { margin: 0 0 3em; }
        header:after { clear: both; content: ""; display: table; }
        header h1 { background: #000; border-radius: 0.25em; color: #FFF; margin: 0 0 1em; padding: 0.5em 0; }
        header address { float: left; font-size: 75%; font-style: normal; line-height: 1.25; margin: 0 1em 1em 0; }
        header address p { margin: 0 0 0.25em; }
        header span, header img { display: block; float: right; }
        header span { margin: 0 0 1em 1em; max-height: 25%; max-width: 60%; position: relative; }
        header img { max-height: 100%; max-width: 100%; }
        article, article address, table.meta, table.inventory { margin: 0 0 3em; }
        article:after { clear: both; content: ""; display: table; }
        article h1 { clip: rect(0 0 0 0); position: absolute; }
        article address { float: left; font-size: 125%; font-weight: bold; }
        table.meta, table.balance { float: right; width: 36%; }
        table.meta:after, table.balance:after { clear: both; content: ""; display: table; }
        table.meta th { width: 40%; }
        table.meta td { width: 60%; }
        table.inventory { clear: both; width: 100%; }
        table.inventory th { font-weight: bold; text-align: center; }
        table.inventory td:nth-child(1) { width: 26%; }
        table.inventory td:nth-child(2) { width: 38%; }
        table.inventory td:nth-child(3) { text-align: right; width: 12%; }
        table.inventory td:nth-child(4) { text-align: right; width: 12%; }
        table.inventory td:nth-child(5) { text-align: right; width: 12%; }
        table.balance th, table.balance td { width: 50%; }
        table.balance td { text-align: right; }
        aside h1 { border: none; border-width: 0 0 1px; margin: 0 0 1em; border-color: #999; border-bottom-style: solid; }
        @media print { * { -webkit-print-color-adjust: exact; } html { background: none; padding: 0; } body { box-shadow: none; margin: 0; } span:empty { display: none; } }
        @page { margin: 0; }
    </style>
</head>
<body>
    <header>
        <h1>Information of Guest</h1>
        <address>
            <p>The Kitchen</p>
            <p>H-Pocket Market, Sarita Vihar, Delhi 110076</p>
            <p>(+94) 65 222 44 55</p>
        </address>
        <span><img alt="" src="assets/img/the_kitchen_logo.png"></span>
    </header>
    <article>
        <h1></h1>
        <address>
            <p><br></p>
            <p>Customer Name: - <?php echo $title.$fname." ".$lname; ?><br></p>
        </address>
        <table class="meta">
            <tr><th><span>Customer ID</span></th><td><span><?php echo $id; ?></span></td></tr>
            <tr><th><span>Time</span></th><td><span><?php echo $tme; ?></span></td></tr>
            <tr><th><span>Date</span></th><td><span><?php echo $dte; ?></span></td></tr>
        </table>
        <table>
            <tr><td>Customer phone: - <?php echo $phone; ?></td><td>Customer email: - <?php echo $email; ?></td></tr>
            <tr><td>Customer Country: - <?php echo $country; ?></td><td>Customer National: - <?php echo $national; ?></td></tr>
        </table>
        <br><br>
        <table class="inventory">
            <thead><tr><th><span>Table type</span></th></tr></thead>
            <tbody>
                <tr><td><span><?php echo $tbltyp; ?></span></td></tr>
                <tr><td><span><?php echo $purpose; ?></span></td></tr>
                <tr><td><span><?php echo $meal; ?></span></td></tr>
            </tbody>
        </table>
    </article>
    <aside>
        <h1><span>Contact us</span></h1>
        <div>
            <p align="center">Email: ws2basic@gmail.com || Web: www.thekitchen.com || Phone: +011 22425481</p>
        </div>
    </aside>
</body>
</html>
<?php ob_end_flush(); ?>
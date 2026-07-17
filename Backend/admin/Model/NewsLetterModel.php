<?php
require_once '../Entity/NewsLetterEntity.php';
class NewsLetterModel {
    function InsertNewsLetters(NewsLetterEntity $news) {
        require_once '../credentials.php';
        $conn = mysqli_connect($host, $user, $password);
        mysqli_select_db($conn, $database);
        $query = "INSERT INTO newsletterlog(title,subject,news) VALUES('$news->title','$news->subject','$news->news')";
        if(mysqli_query($conn, $query)) {
            echo "<script>alert('NewsLetter has been sent')</script>";
        } else {
            echo "<script>alert('Something went wrong')</script>";
        }
    }
}
?>
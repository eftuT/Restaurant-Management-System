<?php
require_once '../Model/NewsLetterModel.php';
require_once '../Entity/NewsLetterEntity.php';
class NewsLetterController {
    function InsertNewsLetters() {
        $title = $_POST["title"] ?? '';
        $subject = $_POST["subject"] ?? '';
        $news = $_POST["news"] ?? '';
        $NewsLetterEntity = new NewsLetterEntity($title, $subject, $news);
        $NewsLetterModel = new NewsLetterModel();
        $NewsLetterModel->InsertNewsLetters($NewsLetterEntity);
    }
}
?>
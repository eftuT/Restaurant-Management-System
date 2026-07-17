<?php
class NewsLetterEntity {
    public $id;
    public $title;
    public $subject;
    public $news;
    function __construct($title, $subject, $news) {
        $this->title = $title;
        $this->subject = $subject;
        $this->news = $news;
    }
}
?>
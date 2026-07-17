<?php
class TableEntity {
    public $id;
    public $Title;
    public $FName;
    public $LName;
    public $Email;
    public $National;
    public $Country;
    public $Phone;
    public $Tbltyp;
    public $Purpose;
    public $Meal;
    public $time;
    public $date;
    public $status;
    function __construct($Title, $FName, $LName, $Email, $National, $Country, $Phone, $Tbltyp, $Purpose, $Meal, $time, $date, $status) {
        $this->Title = $Title;
        $this->FName = $FName;
        $this->LName = $LName;
        $this->Email = $Email;
        $this->National = $National;
        $this->Country = $Country;
        $this->Phone = $Phone;
        $this->Tbltyp = $Tbltyp;
        $this->Purpose = $Purpose;
        $this->Meal = $Meal;
        $this->time = $time;
        $this->date = $date;
        $this->status = $status;
    }
}
?>
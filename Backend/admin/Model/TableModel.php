<?php
require_once '../Entity/TableEntity.php';
class TableModel {
    function InsertTableRecord(TableEntity $table) {
        require_once '../credentials.php';
        $conn = mysqli_connect($host, $user, $password);
        mysqli_select_db($conn, $database);
        $query = "INSERT INTO tablebook(Title,FName,LName,Email,National,Country,Phone,Tbltyp,Purpose,Meal,time,date,status) 
                  VALUES('$table->Title','$table->FName','$table->LName','$table->Email','$table->National','$table->Country','$table->Phone','$table->Tbltyp','$table->Purpose','$table->Meal','$table->time','$table->date','$table->status')";
        if(mysqli_query($conn, $query)) {
            echo "<script>alert('Your Booking application has been sent')</script>";
        } else {
            echo "<script>alert('Error adding user in database')</script>";
        }
    }
}
?>
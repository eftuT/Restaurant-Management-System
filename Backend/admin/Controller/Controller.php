<?php
require_once '../credentials.php';
class Controller {
    function AvailableTables() {
        $conn = mysqli_connect($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['password']);
        mysqli_select_db($conn, $GLOBALS['database']);
        $query = "SELECT * FROM alltables WHERE status='Available'";
        $res = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($res)) {
            echo "<div class='col-md-3 col-sm-6'>
                    <div class='panel panel-success'>
                        <div class='panel-heading'>
                            <div class='row'>
                                <div class='col-xs-3'><i class='fa fa-table fa-5x'></i></div>
                                <div class='col-xs-9 text-right'>
                                    <h3>" . $row['id'] . "</h3>
                                </div>
                            </div>
                        </div>
                        <div class='panel-footer'>
                            <span class='pull-left'>" . $row['type'] . "</span>
                            <span class='pull-right'>" . $row['purpose'] . "</span>
                            <div class='clearfix'></div>
                        </div>
                    </div>
                </div>";
        }
    }
}
?>
<?php

ob_start();
session_start();

include 'functions.php';
include_once 'db.php';
error_reporting(0);


//get variable from previous page
$eID = $_SESSION['empID'];
$eUsername = $_SESSION['empUName'];

// Dentist info
$dSin = pg_fetch_row(pg_query($dbconn, "SELECT employee_sin FROM employee WHERE employee_id=$eID;"));
$dName = pg_fetch_row(pg_query($dbconn, "SELECT name FROM employee_info WHERE employee_sin='$dSin[0]';"));
$dWorkLocation = pg_fetch_row(pg_query($dbconn, "SELECT address FROM employee_info WHERE employee_sin='$dSin[0]';"));
$dSalary = pg_fetch_row(pg_query($dbconn, "SELECT annual_salary FROM employee_info WHERE employee_sin='$dSin[0]';"));

//get type - dentist/hygienist
$type = pg_fetch_row(pg_query($dbconn, "SELECT employee_type FROM Employee_info WHERE employee_sin = '$dSin[0]';"));
if ($type[0] == 'h'){
	$type = "Hygienist";
}elseif ($type[0] == 'd'){
	$type = "Dentist";
}


//  info about upcoming appointment if the dentist has any
$appointID = pg_fetch_row(pg_query($dbconn, "SELECT appointment_id FROM appointment WHERE dentist_id=$eID AND appointment_status='Booked';"));
if ($appointID != null) {
    $upcomingInfo = pg_fetch_row(pg_query($dbconn, "SELECT date_of_appointment, start_time, end_time FROM appointment WHERE dentist_id=$eID AND appointment_status='Booked';"));
    $appointType = pg_fetch_row(pg_query($dbconn, "SELECT appointment_type FROM appointment WHERE dentist_id=$eID AND appointment_status='Booked';"));
    $pId = pg_fetch_row(pg_query($dbconn, "SELECT date_of_appointment, start_time, end_time FROM appointment WHERE dentist_id=$eID AND appointment_status='Booked';"));
    $pID = pg_fetch_row(pg_query($dbconn, "SELECT patient_id FROM appointment WHERE dentist_id=$eID AND appointment_status='Booked';"));
    $pSin = pg_fetch_row(pg_query($dbconn, "SELECT sin_info FROM Patient WHERE patient_id = '$pID[0]';"));
    $pName = pg_fetch_row(pg_query($dbconn, "SELECT name FROM Patient_info WHERE patient_sin='$pSin[0]';"));
    $appointDesc = pg_fetch_row(pg_query($dbconn, "SELECT appointment_description FROM appointment_procedure WHERE appointment_id=$appointID[0];"));
    $procedName = pg_fetch_row(pg_query($dbconn, "SELECT procedure_name FROM procedure_codes WHERE procedure_code=$appointType[0];"));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCMS - <?php echo $type ?> Homepage</title>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css'>
    <link rel='stylesheet' href='main.css' type="text/css">
</head>
<body>
    <div class="container">
        <div class="logout-btn">
            <a href="logout.php" class="logout-btn-text">Logout</a>
        </div>
        <h1>Hello Dr. <?php echo $dName[0] ?></h1>
        <hr>
        <!-- Displays some information concerning the dentist -->
        <h3>My information</h3>
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="h-100">
                    <div class="boxInfo">
                        <div class="text-muted">Employee position</div>
                        <div class="h3"><?php echo $type ?></div>
                    </div> 
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="h-100">
                    <div class="boxInfo">
                        <div class="text-muted">Current Work Location</div>
                        <div class="h3"><?php echo $dWorkLocation[0] ?> </div>
                    </div> 
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="boxInfo">
                        <div class="text-muted">Current Salary</div>
                        <div class="h3"><?php echo $dSalary[0] ?> </div>
                </div>
            </div>
        </div>
        <hr>
        <!-- Shows upcoming appointment (im gonna implemement when the dentist has no upcoming appointment later) -->
        <div class="card card-header-actions mb-4 mt-4">
            <div class="card-header"><h3>Upcoming appointment</h3></div>
                <div class="card-body px-0">
                    <div class="appointment-info">
                    <?php 
                        if ($appointID != null) {
                            echo "
                            <h5>Patient's Name: $pName[0]</h5>
                            <h5>Date: $upcomingInfo[0]</h5>
                            <h5>Start Time: $upcomingInfo[1]</h5>
                            <h5>End Time: $upcomingInfo[2]</h5>
                            <h5>Procedure to do: $procedName[0]</h5>
                            <h5>Procedure description: $appointDesc[0]</h5>";
                        } else {
                            echo "<h5>You do not have any upcoming appointments...</h5>";
                        }?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
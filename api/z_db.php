<?php
//$con = new mysqli("localhost", "bet_db_user", "Zo3&ni55", "admin_bet_db");
$con = new mysqli("localhost", "root", "", "admin_bet_db");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
 }

date_default_timezone_set('Asia/Manila');
 $datedata= date('Y-m-j G:i:a');
$times= date('g:i:a');
$bdata= date('Y-m-j');
$y= date('Y');
$months= date('m');
$hour= date('G');
$mint= date('i');

?>
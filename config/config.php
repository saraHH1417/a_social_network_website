<?php
ob_start();  // Turns on output buffering
session_start();

$timezone = date_default_timezone_set("Asia/Tehran");

$con = mysqli_connect("localhost", "root", "" , "social"); // connection variable

if(mysqli_connect_errno()) 
{
    echo "connection to database failed" . mysqli_connect_errno();
}

?>
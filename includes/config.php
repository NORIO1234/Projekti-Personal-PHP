<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "Projekti-Personal-PHP";

$conn = mysqli_connect($host, $user, $password, $database);

if(!$conn){
    die("Connection failed");
}

session_start();

?>

<?php
include "../includes/config.php";

$id = $_GET['id'];

$conn->query("DELETE FROM appointments WHERE id=$id");

header("Location: ../dashboard.php");
?>
<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Barber Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{background:#f4f6f9;}

.sidebar{
    width:250px;
    height:100vh;
    background:#111827;
    position:fixed;
    color:white;
    padding:20px;
}

.sidebar a{
    color:white;
    display:block;
    margin:15px 0;
    text-decoration:none;
}

.main{
    margin-left:270px;
    padding:20px;
}

.card{
    border:none;
    border-radius:12px;
}

</style>

</head>
<body>

<div class="sidebar">

<h3>BarberShop</h3>

<a href="../dashboard.php">Dashboard</a>
<a href="../appointments/add.php">Add Appointment</a>
<a href="../logout.php">Logout</a>

</div>

<div class="main">
<?php
$userName = null;
$hasSidebar = isset($_SESSION['user_id']) && isset($conn);
if ($hasSidebar) {
    $stmt = $conn->prepare("SELECT fullname FROM users WHERE id = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($userName);
    $stmt->fetch();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Barber Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?= (defined('BASE_URL') && BASE_URL !== '/') ? BASE_URL : '' ?>/css/style.css" rel="stylesheet">

<style>

body{background:#f4f6f9;}

.navbar-custom {
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.sidebar{
    width:250px;
    height:100vh;
    background:#111827;
    position:fixed;
    color:white;
    padding:20px;
    top:0;
}

.sidebar a{
    color:white;
    display:block;
    margin:15px 0;
    text-decoration:none;
}

.sidebar h3,
.sidebar p {
    color:#f8fafc;
}

.main{
    margin-left:270px;
    padding:20px;
    padding-top:80px;
}

.main-full {
    margin-left:0;
    padding:20px;
    padding-top:80px;
}

.card{
    border:none;
    border-radius:12px;
}

.table thead tr {
    background:#f8fafc;
}

</style>

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= (defined('BASE_URL') && BASE_URL !== '/') ? BASE_URL : '' ?>/index.php">Barber Shop</a>
    <div class="d-flex align-items-center">
        <?php if ($userName): ?>
            <span class="me-3 text-secondary">Welcome, <?= sanitize($userName) ?> <?php if (isAdmin()): ?><span class="badge bg-danger ms-2">Admin</span><?php endif; ?></span>
            <a href="<?= (defined('BASE_URL') && BASE_URL !== '/') ? BASE_URL : '' ?>/logout.php" class="btn btn-outline-secondary btn-sm">Logout</a>
        <?php else: ?>
            <a href="<?= (defined('BASE_URL') && BASE_URL !== '/') ? BASE_URL : '' ?>/login.php" class="btn btn-dark btn-sm me-2">Login</a>
            <a href="<?= (defined('BASE_URL') && BASE_URL !== '/') ? BASE_URL : '' ?>/register.php" class="btn btn-outline-dark btn-sm">Register</a>
        <?php endif; ?>
    </div>
    </div>
</nav>

<?php if ($hasSidebar): ?>
<div class="sidebar">
    <h3>Dashboard</h3>
    <p class="small">Menu</p>
    <a href="<?= (defined('BASE_URL') && BASE_URL !== '/') ? BASE_URL : '' ?>/dashboard.php"><i class="bi bi-graph-up me-2"></i>Dashboard</a>
    <a href="<?= (defined('BASE_URL') && BASE_URL !== '/') ? BASE_URL : '' ?>/appointments/add.php"><i class="bi bi-plus-circle me-2"></i>Add Appointment</a>
    <a href="<?= (defined('BASE_URL') && BASE_URL !== '/') ? BASE_URL : '' ?>/profile.php"><i class="bi bi-person me-2"></i>Profile</a>
    <?php $currentUser = getCurrentUser(); if ($currentUser && $currentUser['role'] === 'admin'): ?>
        <a href="<?= (defined('BASE_URL') && BASE_URL !== '/') ? BASE_URL : '' ?>/admin/users.php"><i class="bi bi-people me-2"></i>Manage Users</a>
    <?php endif; ?>
    <a href="<?= (defined('BASE_URL') && BASE_URL !== '/') ? BASE_URL : '' ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
</div>
<?php endif; ?>

<div class="<?= $hasSidebar ? 'main' : 'main-full' ?>">
    <?= displayFlash() ?>
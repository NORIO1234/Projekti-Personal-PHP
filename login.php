<?php
include "includes/config.php";

if(isset($_POST['login'])){

$email = $_POST['email'];
$pass = $_POST['password'];

$res = $conn->query("SELECT * FROM users WHERE email='$email'");
$user = $res->fetch_assoc();

if($user && password_verify($pass,$user['password'])){

$_SESSION['user_id']=$user['id'];

header("Location: dashboard.php");

}
}
?>

<form method="POST">

<h2>Login</h2>

<input class="form-control mb-2" name="email">
<input class="form-control mb-2" type="password" name="password">

<button class="btn btn-dark" name="login">Login</button>

</form>
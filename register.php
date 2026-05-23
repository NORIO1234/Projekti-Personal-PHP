<?php
include "config.php";

if(isset($_POST['register'])){

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'],PASSWORD_DEFAULT);

    $check = mysqli_query($conn,
    "SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($check)==0){

        $sql = "INSERT INTO users(fullname,email,password)
                VALUES('$fullname','$email','$password')";

        mysqli_query($conn,$sql);

        header("Location: login.php");
    }
}
?>

<?php include "header.php"; ?>

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card shadow p-4">

<h2 class="text-center mb-4">
Register
</h2>

<form method="POST">

<input type="text"
name="fullname"
class="form-control mb-3"
placeholder="Full Name"
required>

<input type="email"
name="email"
class="form-control mb-3"
placeholder="Email"
required>

<input type="password"
name="password"
class="form-control mb-3"
placeholder="Password"
required>

<button type="submit"
name="register"
class="btn btn-dark w-100">
Register
</button>

</form>

</div>

</div>

</div>

<?php include "footer.php"; ?>
<?php
include "../includes/config.php";
include "../includes/auth.php";

$id = $_GET['id'];

$row = $conn->query("SELECT * FROM appointments WHERE id=$id")->fetch_assoc();

if(isset($_POST['update'])){

$conn->query("UPDATE appointments SET
customer_name='{$_POST['customer']}',
service='{$_POST['service']}',
appointment_date='{$_POST['date']}',
status='{$_POST['status']}'
WHERE id=$id");

header("Location: ../dashboard.php");
}
?>

<form method="POST">

<h2>Edit</h2>

<input class="form-control mb-2" name="customer" value="<?= $row['customer_name'] ?>">
<input class="form-control mb-2" name="service" value="<?= $row['service'] ?>">
<input type="datetime-local" class="form-control mb-2" name="date">

<select name="status" class="form-control mb-2">
<option>Pending</option>
<option>Completed</option>
</select>

<button class="btn btn-warning" name="update">Update</button>

</form>
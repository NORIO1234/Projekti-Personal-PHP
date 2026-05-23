<?php
include "../includes/config.php";
include "../includes/auth.php";

if(isset($_POST['save'])){

$conn->query("INSERT INTO appointments
(customer_name,service,appointment_date,status)
VALUES(
'{$_POST['customer']}',
'{$_POST['service']}',
'{$_POST['date']}',
'{$_POST['status']}'
)");

header("Location: ../dashboard.php");
}
?>

<form method="POST">

<h2>Add Appointment</h2>

<input class="form-control mb-2" name="customer" placeholder="Customer">
<input class="form-control mb-2" name="service" placeholder="Service">
<input type="datetime-local" class="form-control mb-2" name="date">

<select name="status" class="form-control mb-2">
<option>Pending</option>
<option>Completed</option>
</select>

<button class="btn btn-dark" name="save">Save</button>

</form>
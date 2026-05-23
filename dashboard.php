<?php
include "includes/config.php";
include "includes/auth.php";
include "includes/header.php";

$result = $conn->query("SELECT * FROM appointments");
?>

<h2 class="mb-4">Dashboard</h2>

<div class="card p-4">

<table class="table table-hover">

<thead>
<tr>
<th>ID</th>
<th>Customer</th>
<th>Service</th>
<th>Date</th>
<th>Status</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

<?php while($row=$result->fetch_assoc()){ ?>

<tr>

<td><?= $row['id'] ?></td>
<td><?= $row['customer_name'] ?></td>
<td><?= $row['service'] ?></td>
<td><?= $row['appointment_date'] ?></td>
<td>
<span class="badge bg-success">
<?= $row['status'] ?>
</span>
</td>

<td>

<a href="appointments/edit.php?id=<?= $row['id'] ?>"
class="btn btn-warning btn-sm">Edit</a>

<a href="appointments/delete.php?id=<?= $row['id'] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete?')">Delete</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<?php include "includes/footer.php"; ?>
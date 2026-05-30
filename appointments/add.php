<?php
include "../includes/config.php";
include "../includes/auth.php";

$customer = '';
$service = '';
date_default_timezone_set('UTC');
$date = date('Y-m-d\TH:i');
$status = 'Pending';

if(isset($_POST['save'])){
    $customer = trim($_POST['customer']);
    $service = trim($_POST['service']);
    $date = $_POST['date'];
    $status = 'Pending';
    $user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;

    if ($customer === '' || $service === '' || $date === '') {
        setFlash('error', 'Please fill all appointment fields.');
    } else {
        $stmt = $conn->prepare("INSERT INTO appointments (customer_name,service,appointment_date,status,user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssi', $customer, $service, $date, $status, $user_id);
        $stmt->execute();
        $stmt->close();

        setFlash('success', 'Appointment added successfully.');
        header('Location: ../dashboard.php');
        exit;
    }
}
?>

<?php include "../includes/header.php"; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow p-4">
            <h2 class="text-center mb-4">Add Appointment</h2>
            <form method="POST">
                <input class="form-control mb-3" name="customer" placeholder="Customer" value="<?= sanitize($customer) ?>" required>
                <input class="form-control mb-3" name="service" placeholder="Service" value="<?= sanitize($service) ?>" required>
                <input type="datetime-local" class="form-control mb-3" name="date" value="<?= sanitize($date) ?>" required>
                <input type="hidden" name="status" value="Pending">
                <button class="btn btn-dark w-100" name="save">Save</button>
            </form>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
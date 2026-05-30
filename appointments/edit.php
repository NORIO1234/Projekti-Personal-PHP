<?php
include "../includes/config.php";
include "../includes/auth.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    setFlash('error', 'Invalid appointment selected.');
    header('Location: ../dashboard.php');
    exit;
}

$stmt = $conn->prepare("SELECT customer_name, service, appointment_date, status, user_id FROM appointments WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($customerName, $serviceName, $appointmentDate, $statusValue, $ownerId);
$stmt->fetch();
$stmt->close();

if (!$customerName) {
    setFlash('error', 'Appointment not found.');
    header('Location: ../dashboard.php');
    exit;
}

$customer = $customerName;
$service = $serviceName;
$date = date('Y-m-d\TH:i', strtotime($appointmentDate));
$status = $statusValue;

// Permission checks: only admin or owner can edit. Regular users can only edit pending appointments.
$currentUser = getCurrentUser();
if (!isAdmin()) {
    if (!$currentUser || $currentUser['id'] !== (int) $ownerId) {
        setFlash('error', 'You are not authorized to edit this appointment.');
        header('Location: ../dashboard.php');
        exit;
    }
    if ($status !== 'Pending') {
        setFlash('error', 'Only appointments with status Pending can be edited by users.');
        header('Location: ../dashboard.php');
        exit;
    }
}

if(isset($_POST['update'])){
    $customer = trim($_POST['customer']);
    $service = trim($_POST['service']);
    $date = $_POST['date'];
    $status = $_POST['status'];

    if ($customer === '' || $service === '' || $date === '') {
        setFlash('error', 'Please fill all appointment fields.');
    } else {
        $stmt = $conn->prepare("UPDATE appointments SET customer_name = ?, service = ?, appointment_date = ?, status = ? WHERE id = ?");
        $stmt->bind_param('ssssi', $customer, $service, $date, $status, $id);
        $stmt->execute();
        $stmt->close();

        setFlash('success', 'Appointment updated successfully.');
        header('Location: ../dashboard.php');
        exit;
    }
}
?>

<?php include "../includes/header.php"; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow p-4">
            <h2 class="text-center mb-4">Edit Appointment</h2>
            <form method="POST">
                <input class="form-control mb-3" name="customer" value="<?= htmlspecialchars($customer) ?>" placeholder="Customer" required>
                <input class="form-control mb-3" name="service" value="<?= htmlspecialchars($service) ?>" placeholder="Service" required>
                <input type="datetime-local" class="form-control mb-3" name="date" value="<?= htmlspecialchars($date) ?>" required>
                <?php if (isAdmin()): ?>
                <select name="status" class="form-control mb-3">
                    <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Accepted" <?= $status === 'Accepted' ? 'selected' : '' ?>>Accepted</option>
                    <option value="Rejected" <?= $status === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                    <option value="Completed" <?= $status === 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select>
                <?php else: ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
                <?php endif; ?>
                <button class="btn btn-warning w-100" name="update">Update</button>
            </form>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
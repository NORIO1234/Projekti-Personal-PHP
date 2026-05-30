<?php
include "../includes/config.php";
include "../includes/auth.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($id <= 0) {
    setFlash('error', 'Invalid appointment selected.');
    header('Location: ../dashboard.php');
    exit;
}

if (!isAdmin()) {
    setFlash('error', 'Only admins can change appointment status.');
    header('Location: ../dashboard.php');
    exit;
}

$valid = ['accept' => 'Accepted', 'reject' => 'Rejected', 'complete' => 'Completed', 'pending' => 'Pending'];
if (!isset($valid[$action])) {
    setFlash('error', 'Invalid action.');
    header('Location: ../dashboard.php');
    exit;
}

$newStatus = $valid[$action];
$stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
$stmt->bind_param('si', $newStatus, $id);
$stmt->execute();
$stmt->close();

setFlash('success', 'Appointment status updated to ' . $newStatus . '.');
header('Location: ../dashboard.php');
exit;

?>

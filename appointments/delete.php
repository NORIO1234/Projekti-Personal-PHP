<?php
include "../includes/config.php";
include "../includes/auth.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    setFlash('error', 'Invalid appointment selected.');
    header('Location: ../dashboard.php');
    exit;
}

// Check ownership/permissions before deleting
$stmt = $conn->prepare("SELECT user_id FROM appointments WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($ownerId);
$stmt->fetch();
$stmt->close();

$currentUser = getCurrentUser();
if (!isAdmin()) {
    if (!$currentUser || $currentUser['id'] !== (int) $ownerId) {
        setFlash('error', 'You are not authorized to delete this appointment.');
        header('Location: ../dashboard.php');
        exit;
    }
}

$stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();

setFlash('success', 'Appointment deleted successfully.');
header('Location: ../dashboard.php');
exit;
?>
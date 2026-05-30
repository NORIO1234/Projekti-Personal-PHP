<?php
include "includes/config.php";
include "includes/auth.php";
include "includes/header.php";

$user = getCurrentUser();
$editMode = false;
$editError = '';

if (isset($_POST['update_profile'])) {
    $fullname = trim($_POST['fullname']);
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($fullname === '') {
        $editError = 'Full name cannot be empty.';
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($storedHash);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($currentPassword, $storedHash)) {
            $editError = 'Current password is incorrect.';
        } elseif ($newPassword !== '' && $newPassword !== $confirmPassword) {
            $editError = 'New passwords do not match.';
        } elseif ($newPassword !== '' && strlen($newPassword) < 6) {
            $editError = 'New password must be at least 6 characters.';
        } else {
            if ($newPassword !== '') {
                $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET fullname = ?, password = ? WHERE id = ?");
                $stmt->bind_param('ssi', $fullname, $newHash, $_SESSION['user_id']);
            } else {
                $stmt = $conn->prepare("UPDATE users SET fullname = ? WHERE id = ?");
                $stmt->bind_param('si', $fullname, $_SESSION['user_id']);
            }
            $stmt->execute();
            $stmt->close();

            setFlash('success', 'Profile updated successfully.');
            header('Location: profile.php');
            exit;
        }
    }
}

if (isset($_GET['edit'])) {
    $editMode = true;
}

$stmt = $conn->prepare("SELECT id, customer_name, service, appointment_date, status, created_at FROM appointments ORDER BY appointment_date DESC LIMIT 10");
$stmt->execute();
$appointmentResult = $stmt->get_result();
$stmt->close();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">My Profile</h2>
        <p class="text-muted mb-0">Manage your account and view your appointment history.</p>
    </div>
    <?php if (!$editMode): ?>
        <a href="profile.php?edit=1" class="btn btn-outline-dark">Edit Profile</a>
    <?php endif; ?>
</div>

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm p-4">
            <h5 class="mb-3">Account Information</h5>
            
            <?php if ($editMode): ?>
                <?php if ($editError): ?>
                    <div class="alert alert-danger mb-3"><?= sanitize($editError) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="fullname" class="form-control" value="<?= sanitize($user['fullname']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?= sanitize($user['email']) ?>" disabled>
                        <small class="text-muted">Email cannot be changed.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password (optional)</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control">
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-dark w-100 mb-2">Save Changes</button>
                    <a href="profile.php" class="btn btn-outline-secondary w-100">Cancel</a>
                </form>
            <?php else: ?>
                <div class="mb-3">
                    <label class="form-label text-muted">Full Name</label>
                    <p class="h5 mb-0"><?= sanitize($user['fullname']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Email</label>
                    <p class="h5 mb-0"><?= sanitize($user['email']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Role</label>
                    <p class="h5 mb-0"><span class="badge bg-secondary"><?= sanitize(ucfirst($user['role'])) ?></span></p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Member Since</label>
                    <p class="h5 mb-0"><?= date('M d, Y', strtotime($user['created_at'])) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-md-7 mb-4">
        <div class="card shadow-sm p-4">
            <h5 class="mb-3">Recent Appointments</h5>
            
            <?php if ($appointmentResult->num_rows === 0): ?>
                <p class="text-muted text-center py-4">No appointments yet. <a href="appointments/add.php">Create one now</a>.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $appointmentResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?= sanitize($row['customer_name']) ?></td>
                                    <td><?= sanitize($row['service']) ?></td>
                                    <td><?= date('M d, Y H:i', strtotime($row['appointment_date'])) ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'Completed'): ?>
                                            <span class="badge bg-success">Completed</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <a href="dashboard.php" class="btn btn-sm btn-outline-dark">View All Appointments</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>

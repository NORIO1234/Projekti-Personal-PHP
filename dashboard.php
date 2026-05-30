<?php
include "includes/config.php";
include "includes/auth.php";
include "includes/header.php";

$statusFilter = trim($_GET['status'] ?? '');
$search = trim($_GET['search'] ?? '');
$searchLike = "%{$search}%";

$totalCount = $conn->query("SELECT COUNT(*) FROM appointments")->fetch_row()[0];
$pendingCount = $conn->query("SELECT COUNT(*) FROM appointments WHERE status = 'Pending'")->fetch_row()[0];
$completedCount = $conn->query("SELECT COUNT(*) FROM appointments WHERE status = 'Completed'")->fetch_row()[0];
$todayCount = $conn->query("SELECT COUNT(*) FROM appointments WHERE DATE(appointment_date) = CURDATE()")->fetch_row()[0];

$baseSelect = "SELECT a.id, a.customer_name, a.service, a.appointment_date, a.status, a.user_id, u.fullname AS owner_name, u.email AS owner_email FROM appointments a LEFT JOIN users u ON a.user_id = u.id";
if ($statusFilter !== '' && $search !== '') {
    $stmt = $conn->prepare($baseSelect . " WHERE a.status = ? AND (a.customer_name LIKE ? OR a.service LIKE ?) ORDER BY a.appointment_date DESC");
    $stmt->bind_param('sss', $statusFilter, $searchLike, $searchLike);
} elseif ($statusFilter !== '') {
    $stmt = $conn->prepare($baseSelect . " WHERE a.status = ? ORDER BY a.appointment_date DESC");
    $stmt->bind_param('s', $statusFilter);
} elseif ($search !== '') {
    $stmt = $conn->prepare($baseSelect . " WHERE a.customer_name LIKE ? OR a.service LIKE ? ORDER BY a.appointment_date DESC");
    $stmt->bind_param('ss', $searchLike, $searchLike);
} else {
    $stmt = $conn->prepare($baseSelect . " ORDER BY a.appointment_date DESC");
}
$stmt->execute();
$result = $stmt->get_result();

$userCount = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
?>

<?php if (isAdmin()): ?>
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <strong>Admin Mode</strong> — You have full access to all appointments and can manage users.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Dashboard</h2>
        <p class="text-muted mb-0">Manage appointments and monitor your team in one place.</p>
    </div>
    <a href="appointments/add.php" class="btn btn-dark">New Appointment</a>
</div>

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm p-4">
            <h6 class="text-uppercase text-muted mb-2">Total Appointments</h6>
            <h3><?= sanitize((string) $totalCount) ?></h3>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm p-4">
            <h6 class="text-uppercase text-muted mb-2">Pending</h6>
            <h3><?= sanitize((string) $pendingCount) ?></h3>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm p-4">
            <h6 class="text-uppercase text-muted mb-2">Completed</h6>
            <h3><?= sanitize((string) $completedCount) ?></h3>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm p-4">
            <h6 class="text-uppercase text-muted mb-2">Today</h6>
            <h3><?= sanitize((string) $todayCount) ?></h3>
        </div>
    </div>
    <?php if (isAdmin()): ?>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm p-4 bg-info-light border-info">
                <h6 class="text-uppercase text-muted mb-2">Total Users</h6>
                <h3><?= sanitize((string) $userCount) ?></h3>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="card shadow-sm p-4 mb-4">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by customer or service" value="<?= sanitize($search) ?>">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-control">
                <option value="">All Statuses</option>
                <option value="Pending" <?= $statusFilter === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Accepted" <?= $statusFilter === 'Accepted' ? 'selected' : '' ?>>Accepted</option>
                <option value="Rejected" <?= $statusFilter === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                <option value="Completed" <?= $statusFilter === 'Completed' ? 'selected' : '' ?>>Completed</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
        <div class="col-md-3 text-end">
            <?php if ($statusFilter !== '' || $search !== ''): ?>
                <a href="dashboard.php" class="btn btn-outline-secondary">Clear</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card shadow-sm p-4">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>ID</th>
                    <th>Owner</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows === 0): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No appointments found. Add one to get started.</td>
                    </tr>
            <?php else: ?>
                    <?php
                        $currentUser = getCurrentUser();
                        $isAdmin = isAdmin();
                        while ($row = $result->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= sanitize((string)$row['id']) ?></td>
                            <td>
                                <?= sanitize($row['owner_name'] ?? '—') ?>
                                <?php if (!empty($row['owner_email'])): ?>
                                    <div class="text-muted small"><?= sanitize($row['owner_email']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= sanitize($row['customer_name']) ?></td>
                            <td><?= sanitize($row['service']) ?></td>
                            <td><?= sanitize($row['appointment_date']) ?></td>
                            <td>
                                <?php if ($row['status'] === 'Completed'): ?>
                                    <span class="badge bg-success"><?= sanitize($row['status']) ?></span>
                                <?php elseif ($row['status'] === 'Accepted'): ?>
                                    <span class="badge bg-primary"><?= sanitize($row['status']) ?></span>
                                <?php elseif ($row['status'] === 'Rejected'): ?>
                                    <span class="badge bg-danger"><?= sanitize($row['status']) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark"><?= sanitize($row['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $canManage = false;
                                    if ($isAdmin) {
                                        $canManage = true;
                                    } elseif ($currentUser && isset($row['user_id']) && $currentUser['id'] === (int)$row['user_id'] && $row['status'] === 'Pending') {
                                        $canManage = true;
                                    }
                                ?>
                                <?php if ($canManage): ?>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="appointments/edit.php?id=<?= sanitize((string)$row['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="appointments/delete.php?id=<?= sanitize((string)$row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</a>
                                        <?php if ($isAdmin): ?>
                                            <?php if ($row['status'] !== 'Accepted'): ?>
                                                <a href="appointments/status.php?id=<?= sanitize((string)$row['id']) ?>&action=accept" class="btn btn-success btn-sm">Accept</a>
                                            <?php endif; ?>
                                            <?php if ($row['status'] !== 'Rejected'): ?>
                                                <a href="appointments/status.php?id=<?= sanitize((string)$row['id']) ?>&action=reject" class="btn btn-outline-danger btn-sm">Reject</a>
                                            <?php endif; ?>
                                            <?php if ($row['status'] !== 'Completed'): ?>
                                                <a href="appointments/status.php?id=<?= sanitize((string)$row['id']) ?>&action=complete" class="btn btn-outline-success btn-sm">Complete</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">No actions</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include "includes/footer.php"; ?>
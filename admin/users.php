<?php
include __DIR__ . "/../includes/config.php";
include __DIR__ . "/../includes/auth.php";

if (!isAdmin()) {
    setFlash('error', 'You do not have permission to access this page.');
    header('Location: ../dashboard.php');
    exit;
}

include __DIR__ . "/../includes/header.php";

if (isset($_POST['update_role'])) {
    $userId = (int) $_POST['user_id'];
    $newRole = in_array($_POST['role'], ['user', 'admin']) ? $_POST['role'] : 'user';

    if ($userId !== $_SESSION['user_id']) {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param('si', $newRole, $userId);
        $stmt->execute();
        $stmt->close();
        setFlash('success', 'User role updated successfully.');
    } else {
        setFlash('error', 'You cannot change your own role.');
    }

    header('Location: users.php');
    exit;
}

$search = trim($_GET['search'] ?? '');
$searchLike = "%{$search}%";

if ($search !== '') {
    $stmt = $conn->prepare("SELECT id, fullname, email, role, created_at FROM users WHERE fullname LIKE ? OR email LIKE ? ORDER BY created_at DESC");
    $stmt->bind_param('ss', $searchLike, $searchLike);
} else {
    $stmt = $conn->prepare("SELECT id, fullname, email, role, created_at FROM users ORDER BY created_at DESC");
}
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">User Management</h2>
        <p class="text-muted mb-0">Manage system users and their roles.</p>
    </div>
</div>

<div class="card shadow-sm p-4 mb-4">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-md-8">
            <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="<?= sanitize($search) ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
        <div class="col-md-2 text-end">
            <?php if ($search !== ''): ?>
                <a href="users.php" class="btn btn-outline-secondary w-100">Clear</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card shadow-sm p-4">
    <?php if ($result->num_rows === 0): ?>
        <p class="text-center text-muted py-4">No users found.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= sanitize($row['fullname']) ?></td>
                            <td><?= sanitize($row['email']) ?></td>
                            <td>
                                <?php if ($row['role'] === 'admin'): ?>
                                    <span class="badge bg-danger">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">User</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                            <td>
                                <?php if ($row['id'] !== $_SESSION['user_id']): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= sanitize((string)$row['id']) ?>">
                                        <select name="role" class="form-select form-select-sm d-inline" style="width: auto;" onchange="this.form.submit();">
                                            <option value="user" <?= $row['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                            <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                        <noscript><button type="submit" class="btn btn-sm btn-primary ms-2">Update</button></noscript>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>

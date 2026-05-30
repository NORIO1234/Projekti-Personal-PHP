<?php
include "includes/config.php";

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$email = '';

if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    if ($email === '' || $pass === '') {
        setFlash('error', 'Please enter both email and password.');
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($userId, $hash);
        $stmt->fetch();
        $stmt->close();

        if ($userId && password_verify($pass, $hash)) {
            $_SESSION['user_id'] = $userId;
            header('Location: dashboard.php');
            exit;
        }

        setFlash('error', 'Invalid email or password.');
    }
}
?>

<?php include "includes/header.php"; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-4">
        <div class="card shadow p-4">
            <h2 class="text-center mb-4">Login</h2>
            <form method="POST" novalidate>
                <input class="form-control mb-3" name="email" placeholder="Email" value="<?= sanitize($email) ?>" required>
                <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
                <button class="btn btn-dark w-100" name="login">Login</button>
            </form>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
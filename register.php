<?php
include "includes/config.php";

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$fullname = '';
$email = '';

if(isset($_POST['register'])){
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($fullname === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        setFlash('error', 'Please enter a valid name, email, and password (at least 6 characters).');
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            setFlash('error', 'This email is already registered.');
            $stmt->close();
        } else {
            $stmt->close();
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users(fullname,email,password) VALUES(?,?,?)");
            $stmt->bind_param('sss', $fullname, $email, $hash);
            $stmt->execute();
            $stmt->close();

            setFlash('success', 'Your account has been created. Please log in.');
            header('Location: login.php');
            exit;
        }
    }
}
?>

<?php include "includes/header.php"; ?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow p-4">
            <h2 class="text-center mb-4">Register</h2>
            <form method="POST" novalidate>
                <input type="text" name="fullname" class="form-control mb-3" placeholder="Full Name" value="<?= sanitize($fullname) ?>" required>
                <input type="email" name="email" class="form-control mb-3" placeholder="Email" value="<?= sanitize($email) ?>" required>
                <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                <button type="submit" name="register" class="btn btn-dark w-100">Register</button>
            </form>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
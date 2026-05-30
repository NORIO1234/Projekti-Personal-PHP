<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "Projekti-Personal-PHP";

$conn = mysqli_connect($host, $user, $password, $database);

if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');

session_start();

$documentRoot = str_replace('\\','/', realpath($_SERVER['DOCUMENT_ROOT']));
$projectRoot = str_replace('\\','/', realpath(dirname(__DIR__)));
$baseUrl = str_replace($documentRoot, '', $projectRoot);
if ($baseUrl === '') {
    $baseUrl = '/';
}
define('BASE_URL', $baseUrl);

function setFlash(string $type, string $message): void {
    $_SESSION['flash'][$type] = $message;
}

function displayFlash(): string {
    $output = '';
    if (!empty($_SESSION['flash'])) {
        foreach ($_SESSION['flash'] as $type => $message) {
            $class = $type === 'success' ? 'alert-success' : 'alert-danger';
            $output .= "<div class=\"alert $class alert-dismissible fade show\" role=\"alert\">" . htmlspecialchars($message) . "<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button></div>";
        }
        unset($_SESSION['flash']);
    }
    return $output;
}

function sanitize(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function getCurrentUser() {
    global $conn;
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    $stmt = $conn->prepare("SELECT id, fullname, email, role, created_at FROM users WHERE id = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['role'] === 'admin';
}

$createUsersTable = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$conn->query($createUsersTable);

$checkRoleColumn = $conn->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'role' AND TABLE_SCHEMA = DATABASE()");
if ($checkRoleColumn && $checkRoleColumn->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN role VARCHAR(50) NOT NULL DEFAULT 'user' AFTER password");
}

$createAppointmentsTable = "CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    customer_name VARCHAR(255) NOT NULL,
    service VARCHAR(255) NOT NULL,
    appointment_date DATETIME NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$conn->query($createAppointmentsTable);

// Ensure user_id column exists for tracking who created the appointment
$checkUserIdColumn = $conn->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'appointments' AND COLUMN_NAME = 'user_id' AND TABLE_SCHEMA = DATABASE()");
if ($checkUserIdColumn && $checkUserIdColumn->num_rows === 0) {
    $conn->query("ALTER TABLE appointments ADD COLUMN user_id INT NULL AFTER id");
}

// Ensure status default is 'Pending' for consistency
$checkStatusDefault = $conn->query("SELECT COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'appointments' AND COLUMN_NAME = 'status' AND TABLE_SCHEMA = DATABASE()");
if ($checkStatusDefault) {
    $row = $checkStatusDefault->fetch_assoc();
    if ($row && $row['COLUMN_DEFAULT'] !== 'Pending') {
        $conn->query("ALTER TABLE appointments MODIFY status VARCHAR(50) NOT NULL DEFAULT 'Pending'");
    }
}

?>

<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // PANG ADMIN
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['role'] = $admin['role'];
        $_SESSION['account_type'] = 'admin';

        if ($admin['role'] === 'Master Admin') {
            header("Location: ../pages/master admin/masteradmin.php");
        } else {
            header("Location: ../pages/admin/admin.php");
        }
        exit;
    }

    // PANG USERS
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['type'] = $user['type']; 
        $_SESSION['account_type'] = 'user';

        if ($user['role'] === 'Premium') {
            header("Location: ../pages/PREMIUM ACCOUNT/premium-dashboard.php");
        } else {
            header("Location: ../pages/user.php");
        }
        exit;
    }

    // Invalid login
    echo "Invalid email or password.";
}
?>

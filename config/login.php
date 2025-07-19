<?php
session_start();
include 'dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: ../index.php?error=Please enter both email and password.");
        exit;
    }

    // Check admin
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    if (!$stmt) die("Prepare failed: " . $conn->error);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin) {
        if ($password === $admin['password']) {
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role'] = $admin['role'];

            if ($admin['role'] === 'Master Admin') {
                header("Location: ../pages/master admin/masteradmin.php?success=Logged in successfully");
            } else {
                header("Location: ../pages/admin/admin.php?success=Logged in successfully");
            }
            exit;
        } else {
            header("Location: ../index.php?error=Incorrect password.");
            exit;
        }
    }

    // Check user
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    if (!$stmt) die("Prepare failed: " . $conn->error);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['type'] = $user['type'];

            if ($user['type'] === 'premium') {
                header("Location: ../pages/PREMIUM ACCOUNT/premium-dashboard.php?success=Logged in successfully");
            } else {
                header("Location: ../pages/user.php?success=Logged in successfully");
            }
            exit;
        } else {
            header("Location: ../index.php?error=Incorrect password.");
            exit;
        }
    }

    // No user or admin found
    header("Location: ../index.php?error=Account not found.");
    exit;
}
?>

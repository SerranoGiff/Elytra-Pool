<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");

    if ($stmt->execute([$username, $email, $password])) {
        echo "User registered successfully!";
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
}
?>

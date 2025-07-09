<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $birthday = $_POST['birthday'];
    $username = $_POST['username'];
    $bio = $_POST['bio'];

    // Update user information
    $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, birthday = ?, username = ?, bio = ? WHERE id = ?");
    if ($stmt->execute([$firstName, $lastName, $birthday, $username, $bio, $userId])) {
        echo "Account settings updated successfully!";
    } else {
        echo "Error updating account settings.";
    }
}
?>

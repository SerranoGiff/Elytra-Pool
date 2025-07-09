<?php
require 'db.php';
session_start();

function logActivity($userId, $action, $details) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $action, $details]);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $action = $_POST['action']; 
    $details = $_POST['details'];

    logActivity($userId, $action, $details);
}
?>

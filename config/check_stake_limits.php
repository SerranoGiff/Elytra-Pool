<?php
session_start();
require 'dbcon.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Count active stakes
$activeStmt = $conn->prepare("SELECT COUNT(*) FROM stakes WHERE user_id = ? AND status = 'active'");
$activeStmt->bind_param("i", $user_id);
$activeStmt->execute();
$activeStmt->bind_result($activeStakes);
$activeStmt->fetch();
$activeStmt->close();

// Count monthly stakes
$monthStart = date('Y-m-01 00:00:00');
$monthEnd = date('Y-m-t 23:59:59');

$monthlyStmt = $conn->prepare("SELECT COUNT(*) FROM stakes WHERE user_id = ? AND created_at BETWEEN ? AND ?");
$monthlyStmt->bind_param("iss", $user_id, $monthStart, $monthEnd);
$monthlyStmt->execute();
$monthlyStmt->bind_result($monthlyStakes);
$monthlyStmt->fetch();
$monthlyStmt->close();

echo json_encode([
    'status' => 'ok',
    'active_stakes' => $activeStakes,
    'monthly_stakes' => $monthlyStakes
]);

<?php
session_start();
include 'dbcon.php';
header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
  echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
  exit;
}

$cost = 499;

// Check current balance
$sql = "SELECT usdt_balance FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
  echo json_encode(['status' => 'error', 'message' => 'User not found']);
  exit;
}

$currentBalance = (float)$user['usdt_balance'];

if ($currentBalance < $cost) {
  echo json_encode(['status' => 'error', 'message' => 'Insufficient balance']);
  exit;
}

// Deduct and upgrade
$newBalance = $currentBalance - $cost;
$update = "UPDATE users SET usdt_balance = ?, type = 'premium' WHERE id = ?";
$updateStmt = $conn->prepare($update);
$updateStmt->bind_param("di", $newBalance, $userId);

if ($updateStmt->execute()) {
  echo json_encode(['status' => 'success', 'message' => 'Upgraded successfully']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Upgrade failed']);
}
?>

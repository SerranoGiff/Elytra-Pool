<?php
session_start();
include 'dbcon.php';
header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
  echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
  exit;
}

$sql = "SELECT btc_balance, eth_balance, usdt_balance, eltr_balance, wallet_balance, last_activity 
        FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
  echo json_encode([
    'status' => 'success',
    'btc' => $user['btc_balance'],
    'eth' => $user['eth_balance'],
    'usdt' => $user['usdt_balance'],
    'eltr' => $user['eltr_balance'],
    'total' => $user['wallet_balance'],
    'last_activity' => $user['last_activity']
  ]);
} else {
  echo json_encode(['status' => 'error', 'message' => 'User not found']);
}
?>

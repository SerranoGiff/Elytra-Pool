<?php
session_start();
include 'dbcon.php';
header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
  echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
  exit;
}

$sql = "SELECT usdt_balance FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
  echo json_encode([
    'status' => 'success',
    'balance' => $user['usdt_balance']
  ]);
} else {
  echo json_encode(['status' => 'error', 'message' => 'User not found']);
}
?>

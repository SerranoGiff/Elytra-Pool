<?php
session_start();
include 'dbcon.php';
header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
  echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
  exit;
}

$sql = "SELECT 'deposit' AS type, amount, network AS currency, created_at
        FROM deposits 
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 20";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
while ($row = $result->fetch_assoc()) {
  $row['direction'] = 'in'; // since deposit
  $transactions[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $transactions]);

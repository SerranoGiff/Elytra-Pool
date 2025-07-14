<?php
session_start();
include 'dbcon.php';
header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
  echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
  exit;
}

// Get current balances from users table
$sql = "SELECT btc_balance, eth_balance, usdt_balance, eltr_balance, wallet_balance, last_activity 
        FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    // Add delay logic here
    $delayMinutes = 3;

    // Get sum of delayed deposits per currency
    $depositQuery = $conn->prepare("
        SELECT network, SUM(amount) AS total 
        FROM deposits 
        WHERE user_id = ? 
          AND TIMESTAMPDIFF(MINUTE, created_at, NOW()) >= ? 
        GROUP BY network
    ");
    $depositQuery->bind_param("ii", $userId, $delayMinutes);
    $depositQuery->execute();
    $depositResult = $depositQuery->get_result();

    $depositTotals = ['btc' => 0, 'eth' => 0, 'usdt' => 0];
    while ($row = $depositResult->fetch_assoc()) {
        $key = strtolower($row['network']);
        if (isset($depositTotals[$key])) {
            $depositTotals[$key] = $row['total'];
        }
    }

    // Calculate total
    $total = (
        (float)$user['eltr_balance'] +
        (float)$depositTotals['btc'] +
        (float)$depositTotals['eth'] +
        (float)$depositTotals['usdt']
    );

    echo json_encode([
        'status' => 'success',
        'btc' => $depositTotals['btc'],
        'eth' => $depositTotals['eth'],
        'usdt' => $depositTotals['usdt'],
        'eltr' => $user['eltr_balance'],
        'total' => $total,
        'last_activity' => $user['last_activity']
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
}

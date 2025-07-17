<?php
session_start();
require 'dbcon.php';
header('Content-Type: application/json');

// Check if user is logged in
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

try {
    // Get user's balances and last activity
    $sql = "SELECT btc_balance, eth_balance, usdt_balance, eltr_balance, last_activity 
            FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("User not found");
    }

    $user = $result->fetch_assoc();

    // Calculate total balance
    $btc  = (float)$user['btc_balance'];
    $eth  = (float)$user['eth_balance'];
    $usdt = (float)$user['usdt_balance'];
    $eltr = (float)$user['eltr_balance'];

    $total = $btc + $eth + $usdt + $eltr;

    echo json_encode([
        'status' => 'success',
        'btc' => $btc,
        'eth' => $eth,
        'usdt' => $usdt,
        'eltr' => $eltr,
        'total' => $total,
        'last_activity' => $user['last_activity']
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

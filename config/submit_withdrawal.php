<?php
session_start();
require 'dbcon.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$network = $_POST['network'] ?? '';
$recipient = $_POST['recipient'] ?? '';
$amount = $_POST['amount'] ?? '';
$action = $_POST['action'] ?? '';

if ($action !== 'withdraw') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
    exit;
}

if (!in_array($network, ['USDT', 'BTC', 'ETH'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid network.']);
    exit;
}

if (!preg_match('/^[a-zA-Z0-9]{20,}$/', $recipient)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid wallet address.']);
    exit;
}

if (!is_numeric($amount) || $amount < 1000) {
    echo json_encode(['status' => 'error', 'message' => 'Minimum withdrawal is ₱1000 USDT, BTC, or ETH.']);
    exit;
}

// Check if user has enough balance
$column = '';
switch ($network) {
    case 'USDT': $column = 'usdt_balance'; break;
    case 'BTC':  $column = 'btc_balance'; break;
    case 'ETH':  $column = 'eth_balance'; break;
}

$sql = "SELECT $column FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($balance);
$stmt->fetch();
$stmt->close();

if ($balance < $amount) {
    echo json_encode(['status' => 'error', 'message' => 'Insufficient balance.']);
    exit;
}

// Insert withdrawal request only (no balance deduction)
try {
    $stmt = $conn->prepare("INSERT INTO withdrawals (user_id, network, amount, recipient_address, status, requested_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("isds", $userId, $network, $amount, $recipient);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['status' => 'success', 'message' => 'Withdrawal request submitted and pending admin approval.']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to submit withdrawal: ' . $e->getMessage()]);
}

$conn->close();
?>

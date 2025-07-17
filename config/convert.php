<?php
session_start();
require 'dbcon.php';
header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$from = strtoupper(trim($data['from'] ?? ''));
$to = strtoupper(trim($data['to'] ?? ''));
$amount = floatval($data['amount'] ?? 0);

$allowedPairs = [
    'USDT' => ['ELYTRA'],
    'BTC' => ['ELYTRA'],
    'ETH' => ['ELYTRA'],
    'ELYTRA' => ['USDT', 'BTC', 'ETH']
];

// Your custom conversion logic
$exchangeRates = [
    'USDT:ELYTRA' => 2,                        // 1 USDT = 2 ELTR
    'BTC:ELYTRA' => 235929.62,                // 1 BTC = 235,929.62 ELTR
    'ETH:ELYTRA' => 13764.70,                 // 1 ETH = 13,764.70 ELTR
    'ELYTRA:USDT' => 0.5,                     // 1 ELTR = 0.5 USDT
    'ELYTRA:BTC' => 1 / 235929.62,            // 1 ELTR = ~0.00000424 BTC
    'ELYTRA:ETH' => 1 / 13764.70              // 1 ELTR = ~0.0000726 ETH
];

if (!isset($allowedPairs[$from]) || !in_array($to, $allowedPairs[$from])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid conversion pair.']);
    exit;
}

if (!is_numeric($amount) || $amount <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid amount.']);
    exit;
}

$key = "$from:$to";
$rate = $exchangeRates[$key] ?? null;

if (!$rate) {
    echo json_encode(['status' => 'error', 'message' => 'Exchange rate not found.']);
    exit;
}

$convertedAmount = round($amount * $rate, 8);

$fromField = strtolower($from) . "_balance";

// Check user balance
$sql = "SELECT $fromField FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || $user[$fromField] < $amount) {
    echo json_encode(['status' => 'error', 'message' => 'Insufficient balance.']);
    exit;
}

// Save conversion request for admin approval
$sql = "INSERT INTO conversion_requests (user_id, from_coin, to_coin, amount, converted_amount, rate) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issddd", $userId, $from, $to, $amount, $convertedAmount, $rate);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => "Request submitted. You'll get $convertedAmount $to after approval."
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to submit request.']);
}
?>

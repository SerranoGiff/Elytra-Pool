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

// ✅ Allowed conversion directions
$allowedPairs = [
    'USDT' => ['ELYTRA'],
    'BTC' => ['ELYTRA'],
    'ETH' => ['ELYTRA'],
    'ELYTRA' => ['USDT', 'BTC', 'ETH']
];

// ✅ Exchange rates (example only; adjust as needed)
$exchangeRates = [
    'USDT:ELYTRA' => 2,
    'BTC:ELYTRA' => 235929.62,
    'ETH:ELYTRA' => 13764.70,
    'ELYTRA:USDT' => 0.5,
    'ELYTRA:BTC' => 1 / 235929.62,
    'ELYTRA:ETH' => 1 / 13764.70
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

// ✅ Correct field mapping
$fieldMap = [
    'USDT' => 'usdt_balance',
    'BTC' => 'btc_balance',
    'ETH' => 'eth_balance',
    'ELYTRA' => 'eltr_balance'
];

$fromField = $fieldMap[$from] ?? null;
$toField = $fieldMap[$to] ?? null;

if (!$fromField || !$toField) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid currency field.']);
    exit;
}

// ✅ Get current balance
$sql = "SELECT $fromField FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user[$fromField] < $amount) {
    echo json_encode(['status' => 'error', 'message' => 'Insufficient balance.']);
    exit;
}

// ✅ Insert conversion request
$insertSql = "INSERT INTO conversion_requests (user_id, from_coin, to_coin, amount, converted_amount, rate) VALUES (?, ?, ?, ?, ?, ?)";
$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param("issddd", $userId, $from, $to, $amount, $convertedAmount, $rate);

if ($insertStmt->execute()) {
    // ✅ Update user balances
    $updateSql = "UPDATE users SET $fromField = $fromField - ?, $toField = $toField + ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ddi", $amount, $convertedAmount, $userId);
    $updateStmt->execute();

    echo json_encode([
        'status' => 'success',
        'message' => "Converted $amount $from to $convertedAmount $to successfully."
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to submit request.']);
}
?>

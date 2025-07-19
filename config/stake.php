<?php
session_start();
require 'dbcon.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$currency = strtoupper(trim($_POST['currency'] ?? '')); // Selected currency label
$amount = floatval(str_replace(',', '', $_POST['amount'] ?? ''));
$lock_days = (int) ($_POST['lock_days'] ?? 0);
$daily_percent = (float) ($_POST['daily_percent'] ?? 0);

// ✅ Accept BTC, ETH, ELTR, USDT, SOL, ADA, DOT
$allowedCurrencies = ['BTC', 'ETH', 'ELTR', 'USDT', 'SOL', 'ADA', 'DOT'];

if (!in_array($currency, $allowedCurrencies) || $amount <= 0 || $lock_days <= 0 || $daily_percent <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input values']);
    exit;
}

$userId = $_SESSION['user_id'];
$balanceColumn = 'eltr_balance'; // Deduct only from ELTR

// Get current ELTR balance
$query = "SELECT eltr_balance FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

$row = $result->fetch_assoc();
$currentBalance = (float) $row['eltr_balance'];

if ($amount > $currentBalance) {
    echo json_encode(['status' => 'error', 'message' => 'Insufficient balance']);
    exit;
}

// Begin transaction
$conn->begin_transaction();

try {
    $newBalance = $currentBalance - $amount;

    // Update ELTR balance
    $update = "UPDATE users SET eltr_balance = ? WHERE id = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("di", $newBalance, $userId);
    $stmt->execute();

    // Insert stake record (use selected currency for label)
    $insert = "INSERT INTO stakes (user_id, currency, amount, lock_days, daily_percent, created_at, end_date) 
               VALUES (?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY))";
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("isdddi", $userId, $currency, $amount, $lock_days, $daily_percent, $lock_days);
    $stmt->execute();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Stake successful']);
} catch (Exception $e) {
    $conn->rollback();
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred while processing your request.']);
} finally {
    $stmt->close();
    $conn->close();
}

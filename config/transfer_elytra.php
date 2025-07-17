<?php
session_start();
require 'dbcon.php';
header('Content-Type: application/json');

$sender_id = $_SESSION['user_id'] ?? null;
$recipient_username = $_POST['username'] ?? '';
$amount = floatval($_POST['amount']);

if (!$sender_id || !$recipient_username || $amount <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
    exit;
}

try {
    // 1. Get recipient ID
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $recipient_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Recipient not found.");
    }

    $recipient = $result->fetch_assoc();
    $receiver_id = $recipient['id'];

    if ($receiver_id == $sender_id) {
        throw new Exception("You cannot transfer to yourself.");
    }

    // 2. Check sender USDT balance
    $stmt = $conn->prepare("SELECT usdt_balance FROM users WHERE id = ?");
    $stmt->bind_param("i", $sender_id);
    $stmt->execute();
    $senderBal = $stmt->get_result()->fetch_assoc()['usdt_balance'];

    if ($senderBal < $amount) {
        throw new Exception("Insufficient USDT balance.");
    }

    // 3. Save transfer request as 'pending'
    $stmt = $conn->prepare("INSERT INTO elytra_transfers (sender_id, receiver_id, amount, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("iid", $sender_id, $receiver_id, $amount);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Transfer request submitted. Awaiting admin approval.']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

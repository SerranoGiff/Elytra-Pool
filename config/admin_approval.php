<?php
require 'dbcon.php';
header('Content-Type: application/json');

$depositId = $_POST['deposit_id'] ?? null;
$transferId = $_POST['elytra_transfer_id'] ?? null;
$withdrawId = $_POST['withdrawal_id'] ?? null;

$action = $_POST['action'] ?? null; // 'approve' or 'reject'

if (!$action || !in_array($action, ['approve', 'reject'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
    exit;
}

// === 1. Handle Deposit Approval / Rejection ===
if ($depositId) {
    $sql = "SELECT user_id, amount, network FROM deposits WHERE id = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $depositId);
    $stmt->execute();
    $result = $stmt->get_result();
    $deposit = $result->fetch_assoc();

    if (!$deposit) {
        echo json_encode(['status' => 'error', 'message' => 'Deposit not found or already processed.']);
        exit;
    }

    if ($action === 'approve') {
        $updateWallet = "UPDATE users SET {$deposit['network']}_balance = {$deposit['network']}_balance + ? WHERE id = ?";
        $stmt2 = $conn->prepare($updateWallet);
        $stmt2->bind_param("di", $deposit['amount'], $deposit['user_id']);
        $stmt2->execute();
    }

    $updateDeposit = "UPDATE deposits SET status = ? WHERE id = ?";
    $stmt3 = $conn->prepare($updateDeposit);
    $stmt3->bind_param("si", $action, $depositId);
    $stmt3->execute();

    echo json_encode(['status' => 'success', 'message' => "Deposit {$action}d successfully."]);
    exit;
}

// === 2. Handle Elytra Transfer Approval / Rejection ===
if ($transferId) {
    $sql = "SELECT sender_id, receiver_id, amount, network FROM elytra_transfers WHERE id = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $transferId);
    $stmt->execute();
    $result = $stmt->get_result();
    $transfer = $result->fetch_assoc();

    if (!$transfer) {
        echo json_encode(['status' => 'error', 'message' => 'Transfer not found or already processed.']);
        exit;
    }

    if ($action === 'approve') {
        $conn->begin_transaction();
        try {
            // Add to receiver
            $add = "UPDATE users SET {$transfer['network']}_balance = {$transfer['network']}_balance + ? WHERE id = ?";
            $stmt2 = $conn->prepare($add);
            $stmt2->bind_param("di", $transfer['amount'], $transfer['receiver_id']);
            $stmt2->execute();

            // Set status approved
            $update = "UPDATE elytra_transfers SET status = 'approved' WHERE id = ?";
            $stmt3 = $conn->prepare($update);
            $stmt3->bind_param("i", $transferId);
            $stmt3->execute();

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Transfer approval failed.']);
            exit;
        }
    } else {
        // Revert sender’s balance
        $refund = "UPDATE users SET {$transfer['network']}_balance = {$transfer['network']}_balance + ? WHERE id = ?";
        $stmt4 = $conn->prepare($refund);
        $stmt4->bind_param("di", $transfer['amount'], $transfer['sender_id']);
        $stmt4->execute();

        $update = "UPDATE elytra_transfers SET status = 'rejected' WHERE id = ?";
        $stmt5 = $conn->prepare($update);
        $stmt5->bind_param("i", $transferId);
        $stmt5->execute();
    }

    echo json_encode(['status' => 'success', 'message' => "Transfer {$action}d successfully."]);
    exit;
}

// === 3. Handle Withdrawal Approval / Rejection ===
if ($withdrawId) {
    $sql = "SELECT user_id, amount, network FROM withdrawals WHERE id = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $withdrawId);
    $stmt->execute();
    $result = $stmt->get_result();
    $withdraw = $result->fetch_assoc();

    if (!$withdraw) {
        echo json_encode(['status' => 'error', 'message' => 'Withdrawal not found or already processed.']);
        exit;
    }

    if ($action === 'approve') {
        $update = "UPDATE withdrawals SET status = 'approved' WHERE id = ?";
        $stmt2 = $conn->prepare($update);
        $stmt2->bind_param("i", $withdrawId);
        $stmt2->execute();
    } else {
        // Refund the amount
        $refund = "UPDATE users SET {$withdraw['network']}_balance = {$withdraw['network']}_balance + ? WHERE id = ?";
        $stmt3 = $conn->prepare($refund);
        $stmt3->bind_param("di", $withdraw['amount'], $withdraw['user_id']);
        $stmt3->execute();

        $update = "UPDATE withdrawals SET status = 'rejected' WHERE id = ?";
        $stmt4 = $conn->prepare($update);
        $stmt4->bind_param("i", $withdrawId);
        $stmt4->execute();
    }

    echo json_encode(['status' => 'success', 'message' => "Withdrawal {$action}d successfully."]);
    exit;
}

// === No valid operation found ===
echo json_encode(['status' => 'error', 'message' => 'No valid request provided.']);
exit;
?>

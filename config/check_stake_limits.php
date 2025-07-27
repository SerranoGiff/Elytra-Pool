<?php
session_start();
require 'dbcon.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Auto-complete expired stakes
$expiredStakes = $conn->prepare("SELECT id, amount, daily_percent, lock_days FROM stakes WHERE user_id = ? AND status = 'active' AND end_date <= NOW()");
$expiredStakes->bind_param("i", $user_id);
$expiredStakes->execute();
$expiredResult = $expiredStakes->get_result();

$totalReturn = 0;
$totalEarnings = 0;
$completedStakeIds = [];

while ($stake = $expiredResult->fetch_assoc()) {
    $principal = (float) $stake['amount'];
    $dailyPercent = (float) $stake['daily_percent'];
    $days = (int) $stake['lock_days'];

    $earnings = ($principal * ($dailyPercent / 100)) * $days;
    $totalEarnings += $earnings;
    $totalReturn += $principal + $earnings;
    $completedStakeIds[] = $stake['id'];
}
$expiredStakes->close();

if ($totalReturn > 0 && count($completedStakeIds) > 0) {
    $conn->begin_transaction();
    try {
        // Update completed stakes
        $inClause = implode(',', array_fill(0, count($completedStakeIds), '?'));
        $types = str_repeat('i', count($completedStakeIds));
        $updateStmt = $conn->prepare("UPDATE stakes SET status = 'completed' WHERE id IN ($inClause)");
        $updateStmt->bind_param($types, ...$completedStakeIds);
        $updateStmt->execute();
        $updateStmt->close();

        // Credit to user wallet
        $credit = $conn->prepare("UPDATE users SET eltr_balance = eltr_balance + ?, last_activity = NOW() WHERE id = ?");
        $credit->bind_param("di", $totalReturn, $user_id);
        $credit->execute();
        $credit->close();

        // Log to transaction_history
        $log = $conn->prepare("INSERT INTO transaction_history (user_id, type, amount, currency, direction, created_at) VALUES (?, 'stake', ?, 'ELTR', 'in', NOW())");
        $log->bind_param("id", $user_id, $totalReturn);
        $log->execute();
        $log->close();

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Stake update failed.']);
        exit;
    }
}

// ✅ Count active stakes
$activeStmt = $conn->prepare("SELECT COUNT(*) FROM stakes WHERE user_id = ? AND status = 'active'");
$activeStmt->bind_param("i", $user_id);
$activeStmt->execute();
$activeStmt->bind_result($activeStakes);
$activeStmt->fetch();
$activeStmt->close();

// ✅ Count monthly stakes (excluding canceled)
$monthStart = date('Y-m-01 00:00:00');
$monthEnd = date('Y-m-t 23:59:59');

$monthlyStmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM stakes 
    WHERE user_id = ? 
      AND created_at BETWEEN ? AND ? 
      AND status != 'canceled'
");
$monthlyStmt->bind_param("iss", $user_id, $monthStart, $monthEnd);
$monthlyStmt->execute();
$monthlyStmt->bind_result($monthlyStakes);
$monthlyStmt->fetch();
$monthlyStmt->close();

// ✅ Final JSON Response
echo json_encode([
    'status' => 'ok',
    'active_stakes' => $activeStakes,
    'monthly_stakes' => $monthlyStakes
]);

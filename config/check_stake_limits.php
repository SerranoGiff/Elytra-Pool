<?php
session_start();
require 'dbcon.php';
header('Content-Type: application/json');

// Set correct timezone
date_default_timezone_set('Asia/Manila');
$conn->query("SET time_zone = '+08:00'");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Auto-complete expired stakes
$expiredStmt = $conn->prepare("
    SELECT id, amount, daily_percent, lock_days 
    FROM stakes 
    WHERE user_id = ? 
      AND status = 'active' 
      AND end_date <= NOW()
");
$expiredStmt->bind_param("i", $user_id);
$expiredStmt->execute();
$expiredResult = $expiredStmt->get_result();

$totalEarnings = 0;
$completedStakeIds = [];

while ($stake = $expiredResult->fetch_assoc()) {
    $principal = (float) $stake['amount'];
    $dailyPercent = (float) $stake['daily_percent'];
    $days = (int) $stake['lock_days'];

    // ELTR earnings = principal * percent * days
    $earnings = ($principal * ($dailyPercent / 100)) * $days;
    $totalEarnings += $earnings;
    $completedStakeIds[] = $stake['id'];
}
$expiredStmt->close();

// ✅ If expired stakes found, update status and credit user
if ($totalEarnings > 0 && count($completedStakeIds) > 0) {
    $conn->begin_transaction();
    try {
        // Build dynamic placeholders
        $inClause = implode(',', array_fill(0, count($completedStakeIds), '?'));
        $types = str_repeat('i', count($completedStakeIds));
        $updateQuery = "UPDATE stakes SET status = 'completed' WHERE id IN ($inClause)";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param($types, ...$completedStakeIds);
        $updateStmt->execute();
        $updateStmt->close();

        // Credit ELTR to user
        $creditStmt = $conn->prepare("UPDATE users SET eltr_balance = eltr_balance + ? WHERE id = ?");
        $creditStmt->bind_param("di", $totalEarnings, $user_id);
        $creditStmt->execute();
        $creditStmt->close();

        // Log it as a transaction
        $logStmt = $conn->prepare("
            INSERT INTO transaction_history (user_id, type, amount, currency, direction, created_at) 
            VALUES (?, 'stake', ?, 'ELTR', 'in', NOW())
        ");
        $logStmt->bind_param("id", $user_id, $totalEarnings);
        $logStmt->execute();
        $logStmt->close();

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

// ✅ Count stakes this month (excluding canceled)
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

// ✅ Return summary
echo json_encode([
    'status' => 'ok',
    'active_stakes' => $activeStakes,
    'monthly_stakes' => $monthlyStakes
]);

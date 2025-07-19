<?php
require 'dbcon.php';

$now = date('Y-m-d H:i:s');

$sql = "SELECT * FROM stakes WHERE status = 'active' AND end_date <= ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$now]);
$stakes = $stmt->fetchAll();

foreach ($stakes as $stake) {
    $userId = $stake['user_id'];
    $currency = strtolower($stake['currency']);
    $amount = $stake['amount'];
    $days = $stake['lock_days'];
    $percent = $stake['daily_percent'];

    $totalEarnings = $amount * ($percent / 100) * $days;
    $totalToReturn = $amount + $totalEarnings;
    $balanceCol = $currency . '_balance';

    $pdo->prepare("UPDATE users SET $balanceCol = $balanceCol + ? WHERE id = ?")->execute([$totalToReturn, $userId]);
    $pdo->prepare("UPDATE stakes SET status = 'completed' WHERE id = ?")->execute([$stake['id']]);
}

<?php
session_start();
require 'dbcon.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'yesterday_earnings'  => 0.00,
        'thirty_day_earnings' => 0.00,
        'lifetime_earnings'   => 0.00
    ]);
    exit;
}

$userId     = $_SESSION['user_id'];
$today      = new DateTime();
$yesterday  = (clone $today)->modify('-1 day');
$day30Ago   = (clone $today)->modify('-30 days');

// Helper: get minimum daily percent for a stake
function getMinimumDailyPercent($amount, $lock_days)
{
    $tiers = [
        3   => ['min' => 200,     'max' => 2999.99,   'daily' => 4.2],
        7   => ['min' => 3000,    'max' => 6999.99,   'daily' => 4.5],
        15  => ['min' => 7000,    'max' => 19999.99,  'daily' => 7.0],
        30  => ['min' => 20000,   'max' => 39999.99,  'daily' => 8.5],
        60  => ['min' => 40000,   'max' => 99999.99,  'daily' => 10.0],
        90  => ['min' => 120000,  'max' => PHP_INT_MAX, 'daily' => 16.0],
    ];
    if (!isset($tiers[$lock_days])) {
        return 0;
    }
    $t = $tiers[$lock_days];
    if ($amount >= $t['min'] && $amount <= $t['max']) {
        return $t['daily'];
    }
    return 0;
}

// Fetch stakes overlapping yesterday or in last 30 days
$query = "
    SELECT amount, lock_days, start_date, end_date
    FROM stakes
    WHERE user_id = ?
      AND (
           (start_date <= ? AND (end_date IS NULL OR end_date >= ?))
        OR (start_date >= ?)
      )
";
$stmt = $conn->prepare($query);
$yStr = $yesterday->format('Y-m-d');
$d30  = $day30Ago->format('Y-m-d');
$stmt->bind_param("ssss", $userId, $yStr, $yStr, $d30);
$stmt->execute();
$res = $stmt->get_result();

$yesterdayTotal  = 0.0;
$thirtyDayTotal  = 0.0;

// Sum earnings for yesterday and last 30 days
while ($row = $res->fetch_assoc()) {
    $amt      = (float)$row['amount'];
    $days     = (int)$row['lock_days'];
    $start    = new DateTime($row['start_date']);
    $end      = $row['end_date'] ? new DateTime($row['end_date']) : new DateTime();
    $pct      = getMinimumDailyPercent($amt, $days);
    if ($pct <= 0) {
        continue;
    }
    $dailyEarning = $amt * ($pct / 100);

    // Yesterday
    if ($start <= $yesterday && $end >= $yesterday) {
        $yesterdayTotal += $dailyEarning;
    }

    // Last 30 days
    $iter = clone $start;
    while ($iter <= $end && $iter <= $today) {
        if ($iter >= $day30Ago) {
            $thirtyDayTotal += $dailyEarning;
        }
        $iter->modify('+1 day');
    }
}

// Compute lifetime earnings: every day of every stake
// === LIFETIME EARNINGS (only archived/completed stakes) ===
$lifetimeTotal = 0.0;

$sql = "
  SELECT
    amount,
    lock_days,
    start_date,
    end_date,
    /* inclusive days count */
    DATEDIFF(end_date, start_date) + 1 AS days_staked
  FROM stakes
  WHERE user_id    = ?
    AND status     = 'completed'
    AND end_date IS NOT NULL
";

$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $userId);
$stmt2->execute();
$res2 = $stmt2->get_result();

while ($row = $res2->fetch_assoc()) {
    $amt        = (float)$row['amount'];
    $daysLocked = (int)$row['lock_days'];
    $daysStaked = (int)$row['days_staked'];
    $dailyPct   = getMinimumDailyPercent($amt, $daysLocked);
    if ($dailyPct <= 0 || $daysStaked <= 0) {
        continue;
    }
    $dailyEarn  = $amt * ($dailyPct / 100);
    $lifetimeTotal += $dailyEarn * $daysStaked;
}

// Now return it (along with yesterday & 30d):
echo json_encode([
    'yesterday_earnings'  => round($yesterdayTotal, 2),
    'thirty_day_earnings' => round($thirtyDayTotal, 2),
    'lifetime_earnings'   => round($lifetimeTotal, 2),
]);

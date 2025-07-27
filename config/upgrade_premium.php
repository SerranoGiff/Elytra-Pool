<?php
session_start();
include 'dbcon.php';
header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
  echo json_encode(['status'=>'error','message'=>'Unauthorized']);
  exit;
}

// Cost of one month’s premium
$cost = 499.0;

// 1. Fetch user’s current USDT balance and expiration
$sql = "SELECT usdt_balance, premium_expiration FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) {
  echo json_encode(['status'=>'error','message'=>'User not found']);
  exit;
}

$currentBalance    = (float)$user['usdt_balance'];
$currentExpiration = $user['premium_expiration']; // may be null

if ($currentBalance < $cost) {
  echo json_encode(['status'=>'error','message'=>'Insufficient balance']);
  exit;
}

// 2. Calculate new expiration:
//    If they’re already premium and not yet expired, extend from their existing expiration.
//    Otherwise start from now.
$now       = new DateTime();
if ($currentExpiration && (new DateTime($currentExpiration)) > $now) {
    $start = new DateTime($currentExpiration);
} else {
    $start = $now;
}
$start->modify('+1 month');
$newExpiration = $start->format('Y-m-d H:i:s');

// 3. Deduct balance, set type and expiration
$newBalance = $currentBalance - $cost;
$updateSql = "
  UPDATE users
     SET usdt_balance       = ?,
         type               = 'premium',
         premium_expiration = ?
   WHERE id = ?
";
$updateStmt = $conn->prepare($updateSql);
$updateStmt->bind_param("dsi", $newBalance, $newExpiration, $userId);

if ($updateStmt->execute()) {
  // 4. Update session and report success
  $_SESSION['type']   = 'premium';
  $_SESSION['expires']= $newExpiration;
  echo json_encode([
    'status'    => 'success',
    'message'   => 'Upgraded successfully',
    'expires'   => $newExpiration
  ]);
} else {
  echo json_encode(['status'=>'error','message'=>'Upgrade failed']);
}
?>

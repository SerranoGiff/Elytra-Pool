<?php
session_start();
include 'dbcon.php';
header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// ✅ Combined query: approved deposits + withdrawals + transfers + conversions
$sql = "
    (
      SELECT 'deposit' AS type, amount, network AS currency, created_at, 'in' AS direction
      FROM deposits 
      WHERE user_id = ? AND status = 'approved'
    )
    UNION ALL
    (
      SELECT 'withdraw' AS type, amount, network AS currency, requested_at AS created_at, 'out' AS direction
      FROM withdrawals 
      WHERE user_id = ? AND status = 'approved'
    )
    UNION ALL
    (
      SELECT 'transfer' AS type, amount, 'ELYTRA' AS currency, transferred_at AS created_at,
             CASE 
               WHEN receiver_id = ? THEN 'in'
               ELSE 'out'
             END AS direction
      FROM elytra_transfers 
      WHERE (sender_id = ? OR receiver_id = ?) AND status = 'approved'
    )
    UNION ALL
    (
      SELECT 'convert' AS type, amount, from_coin AS currency, created_at AS approved_at, 'out' AS direction
      FROM conversion_requests 
      WHERE user_id = ? AND status = 'approved'
    )
    UNION ALL
    (
      SELECT 'convert' AS type, amount, to_coin AS currency, created_at AS approved_at, 'in' AS direction
      FROM conversion_requests 
      WHERE user_id = ? AND status = 'approved'
    )
    ORDER BY created_at DESC
    LIMIT 20
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiiiii", $userId, $userId, $userId, $userId, $userId, $userId, $userId);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $transactions]);
?>

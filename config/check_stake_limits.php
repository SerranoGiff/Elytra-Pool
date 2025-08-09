<?php
session_start();
require 'dbcon.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

// ✅ Only count active stakes (not yet ended)
$activeQuery = "SELECT COUNT(*) as total FROM stakes WHERE user_id = ? AND end_date > NOW()";
$stmt = $conn->prepare($activeQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$activeStakes = (int) $row['total'];

// ✅ Count monthly stakes
$monthlyQuery = "SELECT COUNT(*) as total FROM stakes WHERE user_id = ? AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())";
$stmt = $conn->prepare($monthlyQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$monthlyStakes = (int) $row['total'];

echo json_encode([
    'status' => 'ok',
    'active_stakes' => $activeStakes,
    'monthly_stakes' => $monthlyStakes
]);
?>

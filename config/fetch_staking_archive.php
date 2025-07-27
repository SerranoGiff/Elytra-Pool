<?php
session_start();
header('Content-Type: application/json');

include 'dbcon.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$userId = $_SESSION['user_id'];

// Include: (completed OR canceled) stakes
$query = "SELECT id, currency, amount, daily_percent, created_at, end_date, status
          FROM stakes
          WHERE user_id = ? 
            AND (
              (end_date IS NOT NULL AND end_date <= NOW()) 
              OR status = 'canceled'
            )
          ORDER BY end_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}

echo json_encode($history);

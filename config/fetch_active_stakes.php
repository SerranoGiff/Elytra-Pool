<?php
session_start();
require 'dbcon.php';

header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode([]);
    exit;
}

// Show only stakes where end_date is in the future
$sql = "SELECT id, currency, amount, daily_percent, created_at, end_date 
        FROM stakes 
        WHERE user_id = ? AND end_date > NOW() 
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$stakes = [];
while ($row = $result->fetch_assoc()) {
    $stakes[] = $row;
}

echo json_encode($stakes);

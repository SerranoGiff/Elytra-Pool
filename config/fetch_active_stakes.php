<?php
session_start();
require 'dbcon.php';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT id, currency, amount, daily_percent, created_at, end_date FROM stakes WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$stakes = [];
while ($row = $result->fetch_assoc()) {
    $stakes[] = $row;
}

echo json_encode($stakes);

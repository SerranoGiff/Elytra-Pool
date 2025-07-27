<?php
include 'dbcon.php';
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$data = json_decode(file_get_contents("php://input"), true);
$stake_id = $data['stake_id'] ?? 0;

if (!$stake_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid stake ID']);
    exit;
}

// Only cancel active stakes
$stmt = $conn->prepare("SELECT id FROM stakes WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $stake_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Stake not found or already canceled']);
    exit;
}

// Update the status to canceled
$update = $conn->prepare("UPDATE stakes SET status = 'canceled' WHERE id = ?");
$update->bind_param("i", $stake_id);

if ($update->execute()) {
    echo json_encode(['success' => true, 'message' => 'Stake canceled successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to cancel stake: ' . $conn->error]);
}

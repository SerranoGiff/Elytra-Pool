<?php
session_start();
require 'dbcon.php';
header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

function validate($field) {
    return isset($_POST[$field]) && trim($_POST[$field]) !== '';
}

// Validate required POST fields
$requiredFields = ['full_name', 'birth_date', 'email', 'username', 'country', 'doc_type', 'id_number'];
foreach ($requiredFields as $field) {
    if (!validate($field)) {
        echo json_encode(['success' => false, 'message' => 'Missing field: ' . $field]);
        exit;
    }
}

$fullName = $_POST['full_name'];
$birthDate = $_POST['birth_date'];
$email = $_POST['email'];
$username = $_POST['username'];
$country = $_POST['country'];
$docType = $_POST['doc_type'];
$idNumber = $_POST['id_number'];

// Validate file uploads
if (!isset($_FILES['front_image']) || !isset($_FILES['back_image'])) {
    echo json_encode(['success' => false, 'message' => 'Required files are missing.']);
    exit;
}

$frontImage = $_FILES['front_image'];
$backImage = $_FILES['back_image'];
$selfie = $_FILES['selfie'] ?? null;

// Uploads
$uploadDir = '../uploads/kyc/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

function saveFile($file, $prefix, $uploadDir) {
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($ext, $allowed)) return false;

    $filename = $prefix . '_' . uniqid() . '.' . $ext;
    $path = $uploadDir . $filename;

    return move_uploaded_file($file['tmp_name'], $path) ? $path : false;
}

$frontPath = saveFile($frontImage, 'front', $uploadDir);
$backPath = saveFile($backImage, 'back', $uploadDir);
$selfiePath = $selfie && $selfie['tmp_name'] ? saveFile($selfie, 'selfie', $uploadDir) : null;

if (!$frontPath || !$backPath) {
    echo json_encode(['success' => false, 'message' => 'File upload failed.']);
    exit;
}

$sql = "INSERT INTO kyc_verifications (
    user_id, full_name, birth_date, email, username, country, doc_type, id_number,
    front_image, back_image, selfie, status, submitted_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param(
    "issssssssss",
    $userId,
    $fullName,
    $birthDate,
    $email,
    $username,
    $country,
    $docType,
    $idNumber,
    $frontPath,
    $backPath,
    $selfiePath
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'KYC submitted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}
?>

<?php
session_start();
require 'dbcon.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit;
}

$user_id = $_SESSION['user_id'];

$wallet = $_POST['wallet'] ?? '';
$network = $_POST['network'] ?? '';
$address = $_POST['address'] ?? '';
$amount = $_POST['amount'] ?? '';
$agreed = isset($_POST['agreed']) ? 1 : 0;

if ($wallet == '' || $network == '' || $address == '' || $amount == '') {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

if (!is_numeric($amount) || $amount <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid amount."]);
    exit;
}

// ✅ Fetch user type
$typeQuery = $conn->prepare("SELECT type FROM users WHERE id = ?");
$typeQuery->bind_param("i", $user_id);
$typeQuery->execute();
$typeResult = $typeQuery->get_result();

if ($typeResult->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "User not found."]);
    exit;
}

$userData = $typeResult->fetch_assoc();
$userType = strtolower($userData['type']); // expected 'free' or 'premium'

// ✅ Apply pool fee
$feeRate = ($userType === 'premium') ? 0.019 : 0.05;
$feeAmount = round($amount * $feeRate, 8);
$netAmount = round($amount - $feeAmount, 8);

// ✅ Handle file upload
$receiptPath = '';
if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/receipts/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileTmp = $_FILES['receipt']['tmp_name'];
    $fileName = basename($_FILES['receipt']['name']);
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allowed)) {
        echo json_encode(["success" => false, "message" => "Invalid file type."]);
        exit;
    }

    $newFileName = uniqid() . '.' . $ext;
    $receiptPath = $uploadDir . $newFileName;
    move_uploaded_file($fileTmp, $receiptPath);
}

// ✅ Insert into deposits table
$insert = $conn->prepare("INSERT INTO deposits 
    (user_id, wallet, network, wallet_address, amount, net_amount, fee_amount, receipt, agreed, status, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");

$insert->bind_param(
    "isssddssi", 
    $user_id, $wallet, $network, $address, $amount, $netAmount, $feeAmount, $receiptPath, $agreed
);

$insertSuccess = $insert->execute();

if ($insertSuccess) {
    echo json_encode(["success" => true, "message" => "Deposit submitted. A service fee of $feeAmount has been deducted."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to save deposit."]);
}

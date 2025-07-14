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

// Handle file upload
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

// Insert into deposits table
$insert = $conn->prepare("INSERT INTO deposits 
    (user_id, wallet, network, wallet_address, amount, receipt, agreed) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");
$insert->bind_param("isssdsi", $user_id, $wallet, $network, $address, $amount, $receiptPath, $agreed);
$insertSuccess = $insert->execute();

if ($insertSuccess) {
    // Update user balances
    $column = '';
    switch (strtolower($network)) {
        case 'btc': $column = 'btc_balance'; break;
        case 'eth': $column = 'eth_balance'; break;
        case 'usdt': $column = 'usdt_balance'; break;
        default:
            echo json_encode(["success" => false, "message" => "Invalid network for balance update."]);
            exit;
    }

    // Update selected crypto balance
    $update = $conn->prepare("UPDATE users 
        SET $column = $column + ?, 
            wallet_balance = wallet_balance + ?, 
            last_activity = NOW(), 
            updated_at = NOW() 
        WHERE id = ?");
    $update->bind_param("ddi", $amount, $amount, $user_id);
    $update->execute();

    echo json_encode(["success" => true, "message" => "Deposit submitted. It will reflect in your wallet in 3 minutes."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to save deposit."]);
}

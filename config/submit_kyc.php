<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $country = $_POST['country'];
    $idType = $_POST['id_type'];
    $idNumber = $_POST['id_number'];

    // Handle file uploads
    $idFront = $_FILES['id_front']['name'];
    $idBack = $_FILES['id_back']['name'];
    $selfie = $_FILES['selfie']['name'];

    // Define upload directory
    $uploadDir = 'uploads/';

    // Move uploaded files
    move_uploaded_file($_FILES['id_front']['tmp_name'], $uploadDir . $idFront);
    move_uploaded_file($_FILES['id_back']['tmp_name'], $uploadDir . $idBack);
    if (!empty($_FILES['selfie']['name'])) {
        move_uploaded_file($_FILES['selfie']['tmp_name'], $uploadDir . $selfie);
    }

    // Insert KYC data into the database
    $stmt = $pdo->prepare("INSERT INTO kyc (user_id, full_name, email, username, country, id_type, id_number, id_front, id_back, selfie) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$userId, $fullName, $email, $username, $country, $idType, $idNumber, $idFront, $idBack, $selfie])) {
        echo "KYC documents submitted successfully!";
    } else {
        echo "Error submitting KYC documents.";
    }
}
?>

<?php
session_start();
include 'dbcon.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $birthday = $_POST['birthday'] ?? null;
    $username = trim($_POST['username'] ?? '');
    $aboutMe = trim($_POST['aboutMe'] ?? '');
    $walletAddress = trim($_POST['walletAddress'] ?? '');
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    if (!empty($newPassword) && $newPassword !== $confirmPassword) {
        http_response_code(400); // Bad Request
        exit;
    }

    $passwordClause = '';
    $profileClause = '';
    $params = [$firstName, $lastName, $birthday, $username, $aboutMe, $walletAddress];
    $types = 'ssssss';

    if (!empty($newPassword)) {
        $passwordClause = ", password = ?";
        $params[] = $newPassword; // Note: unhashed (for demo only)
        $types .= 's';
    }

    if (isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] === 0) {
        $ext = pathinfo($_FILES['profilePhoto']['name'], PATHINFO_EXTENSION);
        $photoName = 'user_' . $userId . '.' . $ext;
        $uploadDir = '../uploads/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
        $targetPath = $uploadDir . $photoName;

        if (move_uploaded_file($_FILES['profilePhoto']['tmp_name'], $targetPath)) {
            $relativePath = 'uploads/' . $photoName;
            $profileClause = ", profile_photo = ?";
            $params[] = $relativePath;
            $types .= 's';
        }
    }

    $sql = "
        UPDATE users 
        SET 
            first_name = ?, 
            last_name = ?, 
            birthday = ?, 
            username = ?, 
            about_me = ?, 
            wallet_address = ?
            $passwordClause
            $profileClause
        WHERE id = ?
    ";

    $params[] = $userId;
    $types .= 'i';

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        http_response_code(200); // Success
    } else {
        http_response_code(500); // Internal Server Error
    }
}
?>

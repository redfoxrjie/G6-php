<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include '../connectCid101g6.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $userId = $data['userId'];
    $orginalPsw = $data['orginalPsw'];

    $stmt = $pdo->prepare("SELECT u_psw FROM user WHERE u_id = ?");
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        if (password_verify($orginalPsw, $result['u_psw'])) {
            echo json_encode(['success' => true]);
        } else {
            error_log("Password verification failed for userId: $userId"); // Add logging
            echo json_encode(['success' => false, 'message' => 'Invalid old password']);
        }
    } else {
        error_log("User not found for userId: $userId"); // Add logging
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

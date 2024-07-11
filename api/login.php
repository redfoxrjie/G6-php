<?php
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

session_start();
include("../connectCid101g6.php");

if (!isset($_POST["u_account"]) || !isset($_POST["u_psw"])) {
    echo json_encode(['code' => 0, 'msg' => '帳號或密碼未提供']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT u_id, u_account, u_email, u_psw, u_phone, u_nickname, u_birthday, u_country, u_status, u_avatar FROM user WHERE u_account = :uAccount AND u_psw = :uPsw");
    $stmt->bindValue(":uAccount", $_POST["u_account"]);
    $stmt->bindValue(":uPsw", $_POST["u_psw"]);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        unset($user['u_psw']); // 移除密碼欄位以保安全
        echo json_encode(['code' => 1, 'session_id' => session_id(), 'memInfo' => $user]);
    } else {
        echo json_encode(['code' => 0, 'msg' => '帳號未找到或密碼錯誤']);
    }
} catch (PDOException $e) {
    echo json_encode(['code' => 0, 'msg' => '資料庫錯誤: ' . $e->getMessage()]);
}
?>

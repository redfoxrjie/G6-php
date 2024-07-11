<?php
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

include '../connectCid101g6.php'; 

$username = $_POST['username'] ?? '';
$account = $_POST['account'] ?? '';
$password = $_POST['password'] ?? '';
$nickname = $_POST['nickname'] ?? '';

if (empty($username) || empty($account) || empty($password) || empty($nickname)) {
    http_response_code(400); 
    echo json_encode(['code' => 0, 'msg' => '所有欄位都是必填的']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO user (u_account, u_psw, u_email, u_nickname) VALUES (:account, :password, :account, :nickname)");
    $stmt->execute([':account' => $account, ':password' => $password, ':nickname' => $nickname]);

    if ($stmt->rowCount()) {
        echo json_encode(['code' => 1, 'msg' => '註冊成功']);
    } else {
        echo json_encode(['code' => 0, 'msg' => '註冊失敗']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['code' => 0, 'msg' => '資料庫錯誤: ' . $e->getMessage()]);
}
?>

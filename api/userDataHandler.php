<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 引入資料庫連接檔案
include '../connectCid101g6.php';

// 處理 OPTIONS 請求（預檢請求）
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 根據請求方法處理邏輯
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 獲取用戶資料
    if (!isset($_GET['userId'])) {
        echo json_encode(["success" => false, "message" => "No userId provided"]);
        exit(0);
    }

    $userId = $_GET['userId'];
    $stmt = $pdo->prepare("SELECT u_background, u_avatar, u_nickname AS memNickname, u_birthday AS birthdate, u_email AS email, u_phone AS mobile FROM user WHERE u_id = ?");
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $response = [
            'success' => true,
            'bannerImage' => $result['u_background'],
            'headshotImage' => $result['u_avatar'],
            'userData' => [
                'memNickname' => $result['memNickname'],
                'birthdate' => $result['birthdate'],
                'email' => $result['email'],
                'mobile' => $result['mobile']
            ]
        ];
    } else {
        $response = ["success" => false, "message" => "User not found"];
    }

    echo json_encode($response);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 更新用戶資料
    $data = json_decode(file_get_contents("php://input"), true);
    $userId = $data['userId'];

    try {
        $stmt = $pdo->prepare("UPDATE users SET u_nickname = :memNickname, u_birthday = :birthdate, u_email = :email, u_phone = :mobile WHERE u_id = :userId");
        $stmt->bindParam(':memNickname', $data['memNickname'], PDO::PARAM_STR);
        $stmt->bindParam(':birthdate', $data['birthdate'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(':mobile', $data['mobile'], PDO::PARAM_STR);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "無效的請求方法。"]);
}
?>

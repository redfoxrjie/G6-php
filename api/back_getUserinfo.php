<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 引入資料庫連接檔案
include '../connectCid101g6.php';

// 處理 OPTIONS 請求（預檢請求）
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 獲取頁碼和每頁顯示的數量
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $perPage = isset($_GET['perPage']) ? intval($_GET['perPage']) : 10;
    $offset = ($page - 1) * $perPage;

    // 獲取會員數據
    $stmt = $pdo->prepare("SELECT u_id AS id, u_nickname AS name, u_phone AS phone, u_email AS email, u_status AS status FROM user LIMIT :offset, :perPage");
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($members as &$member) {
        $member['active'] = $member['status'] === '是'; // 將 '是' 和 '否' 轉換為布爾值
    }

    // 獲取總會員數量
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM user");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    echo json_encode(["success" => true, "members" => $members, "total" => $total]);
    exit(0);
} else {
    echo json_encode(["success" => false, "message" => "無效的請求方法。"]);
}
?>

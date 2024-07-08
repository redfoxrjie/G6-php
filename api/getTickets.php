<?php
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// 包含資料庫連接檔案
include '../connectCid101g6.php'; 

// // 撰寫SQL查詢
// $sql = 'SELECT t_id, t_name, t_price, t_image FROM tickets';

// 執行查詢並提取資料
try {
    // 建立pdo物件
    $pdo = new PDO($dsn, $user, $password, $options);

    // 準備sql指令
    $sql = "SELECT t_id, t_name, t_title, t_price, t_image, t_viewers FROM tickets";

    // 建立pdo statement
    $promos = $pdo->query($sql);
    $tickets = $promos->fetchAll(PDO::FETCH_ASSOC);

    // 轉換成json檔
    echo json_encode(['tickets' => $tickets]);

} catch (PDOException $e) {
    // 捕捉錯誤
    echo json_encode(['error' => 'Database connection error: ' . $e->getMessage()]);
}
?>

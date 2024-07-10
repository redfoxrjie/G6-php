<?php
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); //允許跨來源 此三種方法ok
header("Access-Control-Allow-Headers: Content-Type"); //允許跨來源
header("Content-Type: application/json"); //回應為json檔

// 包含資料庫連接檔案
include '../connectCid101g6.php'; 

//取前端傳來的id
$data = json_decode(file_get_contents("php://input"), true); //讀取前端發來的請求, json_decode將其轉換為PHP陣列
$t_id = $data['t_id']; //從陣列中提取 t_id 值，並在下方SQL查詢中使用該值來查詢數據庫

// 執行查詢並提取資料
try {
    // 建立pdo物件
    $pdo = new PDO($dsn, $user, $password, $options);

    // 準備sql指令
    $sql = "SELECT t_id, t_name, t_title, t_price, t_content, t_feature1, t_feature2, t_feature3, t_spot, 
    -- 從 tickets 資料表中選擇特定 ID 的票券，並返回其所有相關的欄位數據。
    t_image FROM tickets WHERE t_id = $t_id "; 

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

<?php
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); //允許跨來源 此三種方法ok
header("Access-Control-Allow-Headers: Content-Type"); //允許跨來源
header("Content-Type: application/json"); //回應為json檔

// 包含資料庫連接檔案
include '../connectCid101g6.php'; 

//取前端傳來的id
$data = json_decode(file_get_contents("php://input"), true); 
$o_id = $data['o_id'];

// 執行查詢並提取資料
try {
    // 建立pdo物件
    $pdo = new PDO($dsn, $user, $password, $options);

    // 準備sql指令
    $sql = "SELECT o.o_id, o.o_name, t.t_name,  o.o_count, o.o_price, o.o_date, o.o_status, o.tq_status, o.tq_url
    FROM tickets t,ticketsorder o  
    WHERE o.o_id = $o_id AND t.t_id = o.t_id"; 

    // 建立pdo statement
    $promos = $pdo->query($sql);
    $order = $promos->fetchAll(PDO::FETCH_ASSOC);

    // 轉換成json檔
    echo json_encode(['order' => $order]);

} catch (PDOException $e) {
    // 捕捉錯誤
    echo json_encode(['error' => 'Database connection error: ' . $e->getMessage()]);
}
?>

<?php
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include '../connectCid101g6.php'; 
// 執行查詢並提取資料
try {
    // 建立pdo物件
    $pdo = new PDO($dsn, $user, $password, $options);

    // 準備sql指令
    $sql = "SELECT o_id, o_name,  o_count, o_price, o_date, o_status, o_payment FROM ticketsorder"; 

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

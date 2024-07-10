<?php
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include '../connectCid101g6.php'; 

//請求中的數據
$data = json_decode(file_get_contents("php://input"), true);
$t_id = $data['t_id'];
$t_active = $data['t_active'];

try {

    // 準備sql指令
    $sql = "UPDATE tickets SET t_active = :t_active WHERE t_id = :t_id"; 
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':t_id', $t_id);
    $stmt->bindValue(':t_active', $t_active);

    // 執行sql指令
    $stmt->execute();
    $result = ["error" => false, "msg" => "成功更改"];


} catch (PDOException $e) {
    // 捕捉錯誤
    echo json_encode(['error' => 'Database connection error: ' . $e->getMessage()]);
}
?>

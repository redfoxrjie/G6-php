<?php
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include '../connectCid101g6.php'; 

try {
    
    $pdo = new PDO($dsn, $user, $password, $options);

    $sql = "SELECT cu_id, cu_name, cu_email, cu_message, cu_time, cu_status FROM contact";

    // 建立pdo statement
    $promos = $pdo->query($sql);
    $contactus = $promos->fetchAll(PDO::FETCH_ASSOC);

    // 轉換成json檔
    echo json_encode(['contactus' => $contactus]);

} catch (PDOException $e) {
    // 捕捉錯誤
    echo json_encode(['error' => 'Database connection error: ' . $e->getMessage()]);
}
?>

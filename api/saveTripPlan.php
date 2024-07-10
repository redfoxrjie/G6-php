<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// 引入資料庫連接檔案
include '../connectCid101g6.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['u_id']) || !isset($data['trp_sdate']) || !isset($data['trp_edate']) || !isset($data['trp_area']) || !isset($data['days'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields', 'received_data' => $data]);
    exit();
}

// 提取資料
$u_id = $data['u_id'];
$trp_name = isset($data['trp_name']) ? $data['trp_name'] : '新的行程';
$trp_sdate = $data['trp_sdate'];
$trp_edate = $data['trp_edate'];
$trp_area = $data['trp_area'];
$trp_rate = isset($data['trp_rate']) ? $data['trp_rate'] : 0;
$trp_rate_sum = isset($data['trp_rate_sum']) ? $data['trp_rate_sum'] : 0;
$trp_is_public = isset($data['trp_is_public']) ? $data['trp_is_public'] : 0;
$trp_img = isset($data['trp_img']) ? $data['trp_img'] : '';

try {
    // 開啟交易
    $pdo->beginTransaction();

    // 插入到 trip 資料表
    $stmt = $pdo->prepare("INSERT INTO trip (u_id, trp_name, trp_sdate, trp_edate, trp_area, trp_rate, trp_rate_sum, trp_is_public, trp_img) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$u_id, $trp_name, $trp_sdate, $trp_edate, $trp_area, $trp_rate, $trp_rate_sum, $trp_is_public, $trp_img]);
    
    // 獲取插入的 trip ID
    $trp_id = $pdo->lastInsertId();

    // 插入到 trip_day 和 trip_spot 資料表
    $dayStmt = $pdo->prepare("INSERT INTO trip_day (trp_id, day_num) VALUES (?, ?)");
    $spotStmt = $pdo->prepare("INSERT INTO trip_spot (day_id, osm_id, sp_time, sp_note, sp_order, sp_type, day_num) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($data['days'] as $day) {
        // 插入到 trip_day 資料表
        $dayStmt->execute([$trp_id, $day['day_num']]);
        $day_id = $pdo->lastInsertId();

        foreach ($day['spots'] as $spot) {
            // 格式化 sp_time 為 HH:mm:ss
            $formattedTime = date('H:i:s', strtotime($spot['sp_time']));

            // 插入到 trip_spot 資料表
            $spotStmt->execute([$day_id, $spot['osm_id'], $formattedTime, '', $spot['sp_order'], 0, $spot['day_num']]);
        }
    }

    // 提交交易
    $pdo->commit();

    echo json_encode(['status' => 'success', 'message' => 'Trip plan saved successfully']);
} catch (PDOException $e) {
    // 回滾交易
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Failed to save trip plan: ' . $e->getMessage()]);
}
?>

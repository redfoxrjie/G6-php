<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// 引入資料庫連接檔案
include '../connectCid101g6.php';

// 確保 trp_id 是整數
$trp_id = isset($_GET['trp_id']) ? intval($_GET['trp_id']) : 0;

if ($trp_id <= 0) {
    http_response_code(400); // 傳回 400 Bad Request 狀態碼
    echo json_encode(array("error" => "Invalid trp_id"));
    exit();
}

try {
    // 準備查詢trip資料表
    $sqlTrip = "SELECT * FROM trip WHERE trp_id = ?";
    $stmtTrip = $pdo->prepare($sqlTrip);
    $stmtTrip->execute([$trp_id]);
    $dataTrip = $stmtTrip->fetch(PDO::FETCH_ASSOC);

    if (!$dataTrip) {
        http_response_code(404); // 傳回 404 Not Found 狀態碼
        echo json_encode(array("error" => "Trip not found"));
        exit();
    }

    // 準備查詢 trip_day 資料表
    $sqlTripDay = "SELECT * FROM trip_day WHERE trp_id = ?";
    $stmtTripDay = $pdo->prepare($sqlTripDay);
    $stmtTripDay->execute([$trp_id]);
    $dataTripDay = $stmtTripDay->fetchAll(PDO::FETCH_ASSOC);

    // 準備查詢 trip_spot 資料表，根據每個 trip_day 的 day_id
    $dataTripSpot = [];
    foreach ($dataTripDay as $tripDay) {
        $sqlTripSpot = "SELECT * FROM trip_spot WHERE day_id = ?";
        $stmtTripSpot = $pdo->prepare($sqlTripSpot);
        $stmtTripSpot->execute([$tripDay['day_id']]);
        $tripSpots = $stmtTripSpot->fetchAll(PDO::FETCH_ASSOC);
        $dataTripSpot = array_merge($dataTripSpot, $tripSpots);
    }

    // 合併所有資料
    $responseData = array(
        "trip" => $dataTrip,
        "trip_day" => $dataTripDay,
        "trip_spot" => $dataTripSpot
    );

    // 轉換成 JSON 格式並輸出
    echo json_encode($responseData);

    // 關閉連線和資源
    $stmtTrip->closeCursor();
    $stmtTripDay->closeCursor();
    $stmtTripSpot->closeCursor();
    $pdo = null;
} catch (PDOException $e) {
    http_response_code(500); // 傳回 500 Internal Server Error 狀態碼
    echo json_encode(array("error" => "Database query error: " . $e->getMessage()));
    exit();
}
?>

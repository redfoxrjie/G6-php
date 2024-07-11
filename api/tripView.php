<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: http://localhost:5173");

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

try {
    require_once ("../connectCid101g6.php");
    //準備sql指令
    $sql = "SELECT trip.trp_img, trip.trp_name,trip.trp_area, trip.trp_id, user.u_nickname, user.u_avatar FROM trip JOIN user ON trip.u_id = user.u_id;";
    //代入資料
    $trips = $pdo->query($sql);
    //如果找得資料，取回資料，送出json
    if ($trips->rowCount() > 0) {
        $tripRows = $trips->fetchAll(PDO::FETCH_ASSOC);
        $result =["error" => false,"msg" => "","trips"=>$tripRows];
        echo json_encode($result);
    } else {
        $result =["error" => false,"msg" => "無文章","trips"=>[]];
        echo json_encode($result);
    }
} catch (PDOException $e) {
    $msg = "錯誤原因 : " . $e->getMessage() . ", "
        . "錯誤行號 : " . $e->getLine();
    //$msg = "系統錯誤, 請通知系統維護人員";
    $result = ["error" => false,"msg" => $msg];
    echo json_encode($result);
}

?>
<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: http://localhost:5173");


try {
    require_once ("../connectCid101g6.php");
    $keyword = urldecode($_GET['keyword']);
    //準備sql指令
    //  $sql = "select * from trip where trp_name LIKE :searchKeyBind";
     $sql = "SELECT trip.trp_img, trip.trp_name,trip.trp_area, trip.trp_id, user.u_nickname FROM trip JOIN user ON trip.u_id = user.u_id where trp_name LIKE :searchKeyBind ;";

    $trips = $pdo->prepare($sql);
    $searchKey= '%'.$keyword.'%';
    $trips->bindParam(':searchKeyBind',$searchKey, PDO::PARAM_STR);
    $trips->execute();

    //如果找得資料，取回資料，送出json
    if ($trips->rowCount() > 0) {
        $tripRows = $trips->fetchAll(PDO::FETCH_ASSOC);
        $result =["error" => false,"msg" => "","trips"=>$tripRows,"key"=>$keyword];
        echo json_encode($result);
    } else {
        $result =["error" => false,"msg" => "無文章","trips"=>[],"key"=>$keyword];
        echo json_encode($result);
    }
} catch (PDOException $e) {
    $msg = "錯誤原因 : " . $e->getMessage() . ", "
        . "錯誤行號 : " . $e->getLine();
    //$msg = "系統錯誤, 請通知系統維護人員";
    $result = ["error" => true,"msg" => $msg,"key"=>$keyword];
    echo json_encode($result);
}

?>
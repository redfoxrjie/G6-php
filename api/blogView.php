<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: http://localhost:5173");

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

try {
    require_once ("../connectCid101g6.php");
    //準備sql指令
    $sql = "select * from blog";
    //代入資料
    $blogs = $pdo->query($sql);
    //如果找得資料，取回資料，送出json
    if ($blogs->rowCount() > 0) {
        $blogRows = $blogs->fetchAll(PDO::FETCH_ASSOC);
        $result =["error" => false,"msg" => "","blogs"=>$blogRows];
        echo json_encode($result);
    } else {
        $result =["error" => false,"msg" => "無文章","blogs"=>[]];
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
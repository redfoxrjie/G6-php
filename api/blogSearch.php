<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: http://localhost:5173");


try {
    require_once ("../connectCid101g6.php");
    $keyword = urldecode($_GET['keyword']);
    //準備sql指令
     $sql = "select * from blog where b_title LIKE :searchKeyBind";

    $blogs = $pdo->prepare($sql);
    $searchKey= '%'.$keyword.'%';
    $blogs->bindParam(':searchKeyBind',$searchKey, PDO::PARAM_STR);
    $blogs->execute();

    //如果找得資料，取回資料，送出json
    if ($blogs->rowCount() > 0) {
        $blogRows = $blogs->fetchAll(PDO::FETCH_ASSOC);
        $result =["error" => false,"msg" => "","blogs"=>$blogRows,"key"=>$keyword];
        echo json_encode($result);
    } else {
        $result =["error" => false,"msg" => "無文章","blogs"=>[],"key"=>$keyword];
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
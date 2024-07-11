<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: http://localhost:5173");

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");


try {
    require_once ("../connectCid101g6.php");
    $keyword = urldecode($_GET['keyword']);

    //1.內文資料
    //準備sql指令
    $sql = "select
     blog.b_id, blog.b_title, blog.b_content,blog.b_date,blog.b_img,blog.b_likes,trip.trp_img , user.u_nickname,user.u_avatar,user.u_account
     from blog 
     join trip on blog.trp_id = trip.trp_id 
     join user on blog.u_id = user.u_id
     where blog.b_id=:blogpostBind";
    //代入資料
    $blogs = $pdo->prepare($sql);
    // $blogpost = $keyword;
    $blogs->bindParam(':blogpostBind',$keyword,PDO::PARAM_STR);
    $blogs->execute();

    //如果找得資料，取回資料，送出json
    if ($blogs) {
        $blogpost = $blogs->fetch(PDO::FETCH_ASSOC);

        $sql = "SELECT b_id,rp_id, rp_date, rp_content, u_id 
                FROM response 
                WHERE b_id = :blogpostBind 
                ORDER BY rp_date DESC";
        $responses = $pdo ->prepare($sql);
        $responses->bindParam(':blogpostBind', $keyword,PDO::PARAM_STR);
        $responses->execute();
        $blog_rp = $responses->fetchall(PDO::FETCH_ASSOC);
        // 根據responses的u_id 從user表格索取 u_avatar
        $userIds = array_column($blog_rp, 'u_id');
        if(!empty($userIds)){
            $placeholders = implode(',', array_fill(0, count($userIds), '?'));
            $sql = "select u_id,u_avatar,u_nickname
            from user
            where u_id in ($placeholders)";
            $rp_users = $pdo -> prepare($sql);
            $rp_users ->execute($userIds);
            $users = $rp_users->fetchall(PDO::FETCH_ASSOC);
            $userMap = [];
            //將map中對應的頭像和id存入
            foreach ($users as $user){
                $userMap[$user['u_id']] = [
                    'u_avatar' => $user['u_avatar'],
                    'u_nickname' => $user['u_nickname']
                ];
            }
            // map資料放回留言的'
            foreach($blog_rp as &$rp) {
                $rp['u_avatar'] = $userMap[$rp['u_id']]['u_avatar'] ?? null;
                $rp['u_nickname'] = $userMap[$rp['u_id']]['u_nickname'] ?? null;
            }
        }

        // 傳出資料
        $result =  [
            "error" => false,
            "msg"=>'',
            "blogpost"=> $blogpost,
            "blog_rp"=> $blog_rp
        ];
        echo json_encode($result);
    } else {
        $result =["error" => false,"msg" => "無文章","blogpost"=>[],"blog_rp => []"];
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
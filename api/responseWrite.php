<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");




if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;  // 预检请求不需要处理任何内容，直接退出
}
    $data = json_decode(file_get_contents('php://input'), true);
    $u_id = $data['u_id'] ?? '';
    $rp_content = $data['rp_content'] ?? '';
    $b_id = $data['b_id'] ?? '';

    if (empty($u_id) || empty($rp_content) || empty($b_id) ) {
        http_response_code(400); 
        echo json_encode(['code' => 0, 'msg' => '留言欄位異常無法存入資料']);
        exit;
    }

    try {
        require_once ("../connectCid101g6.php");

        $sql = "INSERT INTO response(u_id, rp_content, b_id) VALUES (:u_id,:rp_content,:b_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':u_id' => $u_id,
             ':rp_content' => $rp_content,
              ':b_id' => $b_id]);
    
        if ($stmt->rowCount()>0) {
            echo json_encode(['code' => 1, 'msg' => '註冊成功']);
        } else {
            echo json_encode(['code' => 0, 'msg' => '註冊失敗']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['code' => 0, 'msg' => '資料庫錯誤: ' . $e->getMessage()]);
    }
    ?>










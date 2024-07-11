<?php
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); //允許跨來源 此三種方法ok
header("Access-Control-Allow-Headers: Content-Type"); //允許跨來源
header("Content-Type: application/json"); //回應為json檔


include '../connectCid101g6.php'; 

//取前端傳來的id
$data = json_decode(file_get_contents("php://input"), true); //讀取前端發來的請求, json_decode將其轉換為PHP陣列

$u_id = $data['u_id'];
$t_id = $data['t_id'];
$o_name = $data['o_name'];
$o_count = $data['o_count'];
$o_price = $data['o_price']; 
$o_payment = $data['o_payment']; 
$o_remarks = $data['o_remarks']; 

try {
    
    $pdo = new PDO($dsn, $user, $password, $options);
    if (!empty($t_id)){

        // 準備sql指令
        // 插入票券資料和QR碼URL

        $sql = "INSERT INTO ticketsorder (u_id, t_id, o_name, o_count, o_price, o_payment, o_remarks)
            VALUES ('$u_id', '$t_id', '$o_name', '$o_count', '$o_price', '$o_payment', '$o_remarks')";
        
        $stmt = $pdo->prepare($sql);
    
        if ($stmt->execute()) {
            $response['message'] = "新票券已成功訂購";
            // 查询最新的 o_id
            $sql2 = "SELECT o_id FROM ticketsorder WHERE t_id = $t_id AND u_id = $u_id ORDER BY o_date DESC LIMIT 1";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute();
            $result = $stmt2->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $o_id = $result['o_id'];
                // $tq_url = "https://quickchart.io/qr?text=http://localhost:5173/OrderQRCode/$o_id";
                $tq_url = "https://quickchart.io/qr?text=https://tibamef2e.com/cid101/g6/front/OrderQRCode/$o_id";

                // 更新包含 tq_url 的记录
                $sql3 = "UPDATE ticketsorder SET tq_url = '$tq_url' WHERE o_id = $o_id";
                
                $stmt3 = $pdo->prepare($sql3);

                if ($stmt3->execute()) {
                    $response['message'] = "QRCode已成功生成並新增";
                    $response['tq_url'] = $tq_url ;
                } else {
                    $response['error'] = "QRCode更新失敗" ;
                }
            } else {
                $response['error'] =  "未找到相對應的 o_id" ;
            }
        } else {
            $response['error'] =  "票券訂購失敗" ;
        }
    }
} catch (PDOException $e) {
    $response['error'] =  "Database connection error:". $e->getMessage() ;
}
echo json_encode($response);

?>

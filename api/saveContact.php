<?php
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");


include '../connectCid101g6.php'; 

$data = json_decode(file_get_contents("php://input"), true); //讀取前端發來的請求, json_decode將其轉換為PHP陣列

// $cu_id = $data['cu_id'];
$cu_name = $data['cu_name'];
$cu_phone = $data['cu_phone'];
$cu_email = $data['cu_email']; 
$cu_class = $data['cu_class']; 
$cu_message = $data['cu_message']; 

try {

    $pdo = new PDO($dsn, $user, $password, $options);
    
    if (!empty($cu_name)){

        $sql = "INSERT INTO contact ( cu_id ,cu_name, cu_phone, cu_email, cu_class, cu_message)
            VALUES (null, '$cu_name', '$cu_phone', '$cu_email', '$cu_class', '$cu_message')";
        
        $stmt = $pdo->prepare($sql);
    
        // $stmt->bindValue(":cu_id", $data["cu_id"]); //安全性 可讀性 維護性
        // $stmt->bindValue(":cu_name", $data["cu_name"]);
        // $stmt->bindValue(":cu_phone", $data["cu_phone"]);
        // $stmt->bindValue(":cu_email", $data["cu_email"]);
        // $stmt->bindValue(":cu_class", $data["cu_class"]);
        // $stmt->bindValue(":cu_content", $data["cu_content"]);
        // $stmt->bindValue(":cu_time", $data["cu_time"]);
    
        // $stmt->bindValue(':cu_name', $cu_name);
    
        if ($stmt->execute()) {
            echo "成功新增";
        } else {
            echo "錯誤: " . $sql . "<br>" . $conn->error;
        }
    }

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection error: ' . $e->getMessage()]);
}


?>

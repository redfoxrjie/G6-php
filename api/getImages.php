<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 假設已經連接了資料庫
$conn = new mysqli("localhost", "root", "", "cid101g6"); // 修改資料庫連接信息
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

$userId = $_GET['userId'];
$stmt = $conn->prepare("SELECT u_background, u_avatar FROM user WHERE u_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$response = ["success" => true];
if ($row = $result->fetch_assoc()) {
    $response['bannerImage'] = $row['u_background'];
    $response['headshotImage'] = $row['u_avatar'];
}

echo json_encode($response);

$stmt->close();
$conn->close();
?>

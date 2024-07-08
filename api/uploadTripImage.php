<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// 引入資料庫連接檔案
include '../connectCid101g6.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['image'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing image data']);
    exit();
}

$imageData = $data['image'];
$decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
$filename = uniqid() . '.jpg';
$filePath = '../images/' . $filename;

if (file_put_contents($filePath, $decodedImage)) {
    echo json_encode(['status' => 'success', 'filename' => $filename]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save image']);
}
?>

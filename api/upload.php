<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// 引入資料庫連接檔案
include '../connectCid101g6.php';

// 測試資料庫連接
try {
    $pdo->query("SELECT 1");
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "資料庫連接失敗：" . $e->getMessage()]);
    exit(0);
}

// 處理 OPTIONS 請求（預檢請求）
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 主要處理邏輯
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $field = $_POST['field'] ?? '';
    $userId = $_POST['userId'] ?? '';

    // 檢查必要欄位是否存在
    if (empty($field) || empty($userId)) {
        echo json_encode(["success" => false, "message" => "缺少必要欄位。field: $field, userId: $userId"]);
        exit(0);
    }

    // 檢查並映射欄位名稱
    $allowedFields = [
        'u_background' => 'u_background',
        'u_avatar' => 'u_avatar'
    ];

    if (!isset($allowedFields[$field])) {
        echo json_encode(["success" => false, "message" => "無效的欄位名稱。"]);
        exit(0);
    }

    $dbField = $allowedFields[$field];

    // 檢查是否有檔案上傳
    if (isset($_FILES['image'])) {
        $fileTmpName = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileError = $_FILES['image']['error'];

        // 檢查文件是否有錯誤
        if ($fileError !== 0) {
            echo json_encode(["success" => false, "message" => "文件上傳錯誤，錯誤碼：$fileError"]);
            exit(0);
        }

        // 檢查文件大小限制
        if (($field === 'u_background' && $fileSize > 2000000) || ($field === 'u_avatar' && $fileSize > 200000)) { // 2MB 和 200KB
            echo json_encode(["success" => false, "message" => "檔案過大。"]);
            exit(0);
        }

        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExt, $allowedExts)) {
            echo json_encode(["success" => false, "message" => "不支持的文件類型。"]);
            exit(0);
        }

        // 使用原始文件名
        $uploadPath = '../images/' . $fileName;

        // 移動上傳的檔案
        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            $relativePath = $fileName;

            // 更新資料庫
            try {
                $sql = "UPDATE user SET $dbField = :filePath WHERE u_id = :userId";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':filePath', $relativePath);
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

                
                if ($stmt->execute()) {
                    echo json_encode(["success" => true, "filePath" => $relativePath]);
                } else {
                    $errorInfo = $stmt->errorInfo();
                    echo json_encode(["success" => false, "message" => "無法更新資料庫。SQL: $sql, userId: $userId, error: " . implode(", ", $errorInfo)]);
                }
            } catch (PDOException $e) {
                echo json_encode(["success" => false, "message" => "資料庫錯誤：" . $e->getMessage()]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "無法移動上傳的檔案。"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "沒有接收到上傳的檔案。"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "無效的請求方法。"]);
}
?>

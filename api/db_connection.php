<?php
// 數據庫連接文件

function getDbConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "g6";

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        // 設置 PDO 錯誤模式為異常
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        error_log("數據庫連接失敗: " . $e->getMessage());
        die(json_encode(['success' => false, 'message' => "連接失敗: " . $e->getMessage()]));
    }
}
?>
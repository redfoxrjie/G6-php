<?php
// 管理員管理主文件

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-Admin-Id");

// 啟用錯誤報告
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 設置錯誤日誌
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/your/error.log');  // 請替換為實際的日誌路徑

require_once 'db_connection.php';
require_once 'admin_functions.php';
require_once 'auth_functions.php';

$pdo = getDbConnection();

$action = $_GET['action'] ?? '';
$headers = getallheaders();
$adminId = $headers['X-Admin-Id'] ?? null;

error_log("接收到的操作: " . $action);
error_log("來自header的管理員ID: " . $adminId);

switch ($action) {
    case 'getAllAdmin':
        getAllAdmins($pdo);
        break;
    case 'addAdmin':
        $data = json_decode(file_get_contents('php://input'), true);
        addAdmin($pdo, $data, $adminId);
        break;
    case 'toggleAdminStatus':
        $data = json_decode(file_get_contents('php://input'), true);
        toggleAdminStatus($pdo, $data, $adminId);
        break;
    default:
        error_log("無效的操作: " . $action);
        echo json_encode(['success' => false, 'message' => '無效的操作']);
}
?>
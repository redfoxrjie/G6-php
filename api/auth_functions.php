<?php
// 認證和授權相關函數

function isSuperAdmin($pdo, $adminId) {
    try {
        $stmt = $pdo->prepare("SELECT a_role FROM admin WHERE a_id = :adminId");
        $stmt->execute([':adminId' => $adminId]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        return $admin && ($admin['a_role'] == '0' || $admin['a_role'] == '0');
    } catch (PDOException $e) {
        error_log("檢查超級管理員失敗: " . $e->getMessage());
        return false;
    }
}

function login($pdo, $data) {
    if (!$data || !isset($data['username']) || !isset($data['password'])) {
        echo json_encode(['success' => false, 'message' => '無效輸入']);
        return;
    }

    try {
        $stmt = $pdo->prepare("SELECT a_id, a_name, a_role, a_status FROM admin WHERE a_name = :username AND a_password = :password AND a_status = 1");
        $stmt->execute([':username' => $data['username'], ':password' => $data['password']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            session_start();
            $_SESSION['authenticated'] = true;
            $_SESSION['admin_id'] = $admin['a_id'];
            echo json_encode(['success' => true, 'admin' => $admin]);
        } else {
            echo json_encode(['success' => false, 'message' => '帳號或密碼錯誤，或帳號已被停用']);
        }
    } catch (PDOException $e) {
        error_log("登錄失敗: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => '登錄失敗: ' . $e->getMessage()]);
    }
}

function checkAuth() {
    session_start();
    if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
        echo json_encode(['authenticated' => true]);
    } else {
        echo json_encode(['authenticated' => false]);
    }
}
?>
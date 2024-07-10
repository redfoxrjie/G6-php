<?php
// 管理員相關功能函數

function getAllAdmins($pdo) {
    try {
        $stmt = $pdo->query("SELECT a_id, a_name, a_role, a_status FROM admin");
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $admins]);
    } catch (PDOException $e) {
        error_log("查詢失敗: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => '查詢失敗: ' . $e->getMessage()]);
    }
}

function addAdmin($pdo, $data, $adminId) {
    if (!$data || !isset($data['name']) || !isset($data['password']) || !isset($data['status'])) {
        echo json_encode(['success' => false, 'message' => '無效輸入']);
        return;
    }

    $isSuperAdmin = isSuperAdmin($pdo, $adminId);
    // 添加日誌來檢查 isSuperAdmin 的結果
    error_log("isSuperAdmin check result: " . ($isSuperAdmin ? 'true' : 'false'));

    if (!$adminId || !$isSuperAdmin) {
        echo json_encode(['success' => false, 'message' => '只有超級管理員可以新增管理員']);
        return;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO admin (a_name, a_password, a_role, a_status) VALUES (:name, :password, :role, :status)");
        $stmt->execute([
            ':name' => $data['name'],
            ':password' => $data['password'],
            ':role' => 1,
            ':status' => $data['status']
        ]);
        $newId = $pdo->lastInsertId();
        echo json_encode(['success' => true, 'message' => '管理員新增成功', 'newId' => $newId]);
    } catch (PDOException $e) {
        error_log("新增管理員失敗: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => '管理員新增失敗: ' . $e->getMessage()]);
    }
}

function toggleAdminStatus($pdo, $data, $adminId) {
    if (!$data || !isset($data['id'])) {
        echo json_encode(['success' => false, 'message' => '無效輸入']);
        return;
    }

    $isSuperAdmin = isSuperAdmin($pdo, $adminId);
    if (!$adminId || !$isSuperAdmin) {
        echo json_encode(['success' => false, 'message' => '只有超級管理員可以更改管理員狀態']);
        return;
    }

    try {
        $stmt = $pdo->prepare("SELECT a_role FROM admin WHERE a_id = :id");
        $stmt->execute([':id' => $data['id']]);
        $targetAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($targetAdmin['a_role'] == '0') {
            echo json_encode(['success' => false, 'message' => '不能更改超級管理員的狀態']);
            return;
        }

        $stmt = $pdo->prepare("UPDATE admin SET a_status = NOT a_status WHERE a_id = :id");
        $stmt->execute([':id' => $data['id']]);
        echo json_encode(['success' => true, 'message' => '管理員狀態更新成功']);
    } catch (PDOException $e) {
        error_log("更新管理員狀態失敗: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => '管理員狀態更新失敗: ' . $e->getMessage()]);
    }
}
?>
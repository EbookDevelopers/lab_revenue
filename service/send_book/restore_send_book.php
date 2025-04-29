<?php
session_start();
require '../../service/connect.php';

if (!isset($_SESSION['staff_id'])) {
    http_response_code(403);
    echo "ไม่ได้รับอนุญาต";
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo "ไม่พบรหัสหนังสือ";
    exit;
}

// Undo delete
$stmt = $conn->prepare("
    UPDATE send_book
    SET is_deleted = 0,
        deleted_at = NULL,
        updated_by = ?,
        updated_at = NOW()
    WHERE id = ?
");

$stmt->bind_param('ii', $_SESSION['staff_id'], $id);

if ($stmt->execute()) {
    echo "กู้คืนสำเร็จ";
} else {
    http_response_code(500);
    echo "เกิดข้อผิดพลาดในการกู้คืน";
}

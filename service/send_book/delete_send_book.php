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

// Soft delete โดยไม่ลบจริง
$stmt = $conn->prepare("
    UPDATE send_book
    SET is_deleted = 1,
        deleted_at = NOW(),
        updated_by = ?,
        updated_at = NOW()
    WHERE id = ?
");

$stmt->bind_param('ii', $_SESSION['staff_id'], $id);

if ($stmt->execute()) {
    echo "ลบข้อมูลสำเร็จ (Soft Delete)";
} else {
    http_response_code(500);
    echo "เกิดข้อผิดพลาดในการลบ";
}

<?php
require '../connect.php';

$id = $_POST['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo "ไม่พบ ID";
    exit;
}

$stmt = $conn->prepare("DELETE FROM specimen WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    echo "ลบข้อมูลสำเร็จ";
} else {
    http_response_code(500);
    echo "เกิดข้อผิดพลาดในการลบ";
}

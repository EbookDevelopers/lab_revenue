<?php
require '../connect.php';

$id = $_POST['id'] ?? null;
$name = trim($_POST['name'] ?? '');
$site = trim($_POST['site'] ?? '');

if (!$id || !$name || !$site) {
    http_response_code(400);
    echo "ข้อมูลไม่ครบถ้วน";
    exit;
}

$stmt = $conn->prepare("UPDATE specimen SET name = ?, site = ? WHERE id = ?");
$stmt->bind_param('ssi', $name, $site, $id);

if ($stmt->execute()) {
    echo "อัปเดตข้อมูลสำเร็จ";
} else {
    http_response_code(500);
    echo "เกิดข้อผิดพลาดในการอัปเดต";
}

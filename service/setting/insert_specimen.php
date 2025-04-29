<?php
require '../connect.php';

$name = trim($_POST['name'] ?? '');
$site = trim($_POST['site'] ?? '');

if (!$name || !$site) {
    http_response_code(400);
    echo "กรุณากรอกข้อมูลให้ครบ";
    exit;
}

$stmt = $conn->prepare("INSERT INTO specimen (name, site) VALUES (?, ?)");
$stmt->bind_param('ss', $name, $site);

if ($stmt->execute()) {
    echo "บันทึกข้อมูลสำเร็จ";
} else {
    http_response_code(500);
    echo "เกิดข้อผิดพลาดในการบันทึก";
}

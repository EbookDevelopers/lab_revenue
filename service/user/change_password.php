<?php
session_start();
require '../connect.php';

$new_password = trim($_POST['new_password'] ?? '');
$staff_id = $_SESSION['staff_id'] ?? null;

if (!$staff_id) {
    http_response_code(401);
    echo 'กรุณาเข้าสู่ระบบใหม่';
    exit;
}

// ตรวจสอบความแข็งแรงของรหัสผ่าน server-side
if (
    strlen($new_password) < 8 ||
    !preg_match('/[A-Z]/', $new_password) ||    // ต้องมีตัวอักษรใหญ่
    !preg_match('/[a-z]/', $new_password) ||    // ต้องมีตัวอักษรเล็ก
    !preg_match('/\d/', $new_password) ||       // ต้องมีตัวเลข
    !preg_match('/[^A-Za-z0-9]/', $new_password) // ต้องมีอักขระพิเศษ
) {
    http_response_code(400);
    echo 'รหัสผ่านใหม่ไม่แข็งแรงพอ (ต้องมี ตัวพิมพ์ใหญ่ เล็ก ตัวเลข และอักขระพิเศษ อย่างน้อย 8 ตัว)';
    exit;
}

// Hash password ใหม่
$hash_password = password_hash($new_password, PASSWORD_DEFAULT);

// อัปเดตรหัสผ่านในฐานข้อมูล
$stmt = $conn->prepare("UPDATE staff SET password = ? WHERE id = ?");
$stmt->bind_param('si', $hash_password, $staff_id);

if ($stmt->execute()) {
    echo 'success';
} else {
    http_response_code(500);
    echo 'เกิดข้อผิดพลาดในการเปลี่ยนรหัสผ่าน';
}

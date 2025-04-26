<?php
session_start();
require 'service/connect.php';
// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (isset($_SESSION['admin_id']) || isset($_SESSION['staff_id'])) {
    if (isset($_SESSION['staff_id'])) {
        $staff_id = $_SESSION['staff_id'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $agent = $_SERVER['HTTP_USER_AGENT'];

        $stmt = $conn->prepare("
            INSERT INTO login_logs (staff_id, action, ip_address, user_agent, log_time)
            VALUES (?, 'logout', ?, ?, NOW())
        ");
        $stmt->bind_param("iss", $staff_id, $ip, $agent);
        $stmt->execute();
    }
    // ทำการ Logout
    echo json_encode(['status' => 'success', 'message' => 'ออกจากระบบสำเร็จ']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'ไม่มีการเข้าสู่ระบบ']);
}
session_destroy(); // ทำลาย session
exit();

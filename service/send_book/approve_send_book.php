<?php
session_start();
require '../../service/connect.php';

if (!isset($_SESSION['staff_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'ไม่ได้รับอนุญาต']);
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบรหัสหนังสือ']);
    exit;
}

$stmt = $conn->prepare("UPDATE send_book SET status = 'Approved', updated_by = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param('ii', $_SESSION['staff_id'], $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอนุมัติ']);
}

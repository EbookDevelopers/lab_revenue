<?php
require '../connect.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID ไม่ถูกต้อง']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM send_book WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();

if ($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'ไม่พบข้อมูล']);
}

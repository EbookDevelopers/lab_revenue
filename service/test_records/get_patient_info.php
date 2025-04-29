<?php
require '../connect.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo 'ไม่พบผู้ป่วย';
    exit;
}

$stmt = $conn->prepare("SELECT nap_number, hn, age, sex FROM patients WHERE id = ? AND is_deleted = 0");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$patient = $res->fetch_assoc();

if ($patient) {
    header('Content-Type: application/json');
    echo json_encode($patient);
} else {
    http_response_code(404);
    echo 'ไม่พบข้อมูล';
}

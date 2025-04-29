<?php
require '../connect.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo "ไม่พบ ID";
    exit;
}

$stmt = $conn->prepare("SELECT id, name, site FROM specimen WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$specimen = $result->fetch_assoc();

if ($specimen) {
    header('Content-Type: application/json');
    echo json_encode($specimen);
} else {
    http_response_code(404);
    echo "ไม่พบข้อมูล Specimen";
}

<?php
require '../connect.php';

$id = $_GET['id'] ?? '';

if (!$id) {
    http_response_code(400);
    echo 'invalid id';
    exit;
}

$stmt = $conn->prepare("SELECT id, name FROM situation WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($data);

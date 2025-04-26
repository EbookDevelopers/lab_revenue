<?php
require '../connect.php';

$type = $_GET['type'] ?? '';

switch ($type) {
    case 'department':
        $stmt = $conn->prepare("SELECT id, name FROM department ORDER BY name ASC");
        break;
    case 'duty':
        $stmt = $conn->prepare("SELECT id, name FROM duty ORDER BY name ASC");
        break;
    case 'permission':
        $stmt = $conn->prepare("SELECT id, name FROM permission ORDER BY id ASC");
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid type']);
        exit;
}

$stmt->execute();
$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);

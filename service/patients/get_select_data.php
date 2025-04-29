<?php
require '../connect.php';

$type = $_GET['type'] ?? '';

switch ($type) {
    case 'hospital':
        $stmt = $conn->prepare("SELECT id, name, code FROM hospital ORDER BY name ASC LIMIT 100");
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

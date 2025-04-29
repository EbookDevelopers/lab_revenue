<?php
require '../connect.php';

$search = $_GET['search'] ?? '';

$stmt = $conn->prepare("
    SELECT id, code, name
    FROM hospital
    WHERE name LIKE CONCAT('%', ?, '%') OR code LIKE CONCAT('%', ?, '%')
    ORDER BY name ASC
    LIMIT 50
");
$stmt->bind_param('ss', $search, $search);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($row = $res->fetch_assoc()) {
    $text = "[{$row['code']}] {$row['name']}";
    $data[] = [
        'id' => $row['id'],
        'text' => $text
    ];
}

header('Content-Type: application/json');
echo json_encode($data);

<?php
require '../connect.php';

$search = $_GET['search'] ?? '';

$stmt = $conn->prepare("
    SELECT id, nap_number, hn
    FROM patients
    WHERE (nap_number LIKE CONCAT('%', ?, '%') OR hn LIKE CONCAT('%', ?, '%')) 
      AND is_deleted = 0
    ORDER BY nap_number ASC
    LIMIT 50
");
$stmt->bind_param('ss', $search, $search);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = [
        'id' => $row['id'],
        'text' => "[HN: {$row['hn']}] {$row['nap_number']}"
    ];
}

header('Content-Type: application/json');
echo json_encode($data);

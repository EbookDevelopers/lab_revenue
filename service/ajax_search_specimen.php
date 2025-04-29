<?php
require 'connect.php';

$search = $_GET['search'] ?? '';

$sql = "
    SELECT id, name 
    FROM specimen 
    WHERE name LIKE ?
    ORDER BY name ASC
    LIMIT 30
";

$stmt = $conn->prepare($sql);
$likeSearch = "%$search%";
$stmt->bind_param('s', $likeSearch);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);

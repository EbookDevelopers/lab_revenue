<?php
require 'connect.php';

$search = $_GET['search'] ?? '';

$sql = "
    SELECT id, CONCAT(code, ' ', name) AS name 
    FROM hospital 
    WHERE (name LIKE ? OR code LIKE ?)
    ORDER BY name ASC
    LIMIT 30
";

$stmt = $conn->prepare($sql);
$likeSearch = "%$search%";
$stmt->bind_param('ss', $likeSearch, $likeSearch);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);

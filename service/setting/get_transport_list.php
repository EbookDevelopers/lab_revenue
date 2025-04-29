<?php
require '../connect.php';

$result = $conn->query("SELECT id, name FROM transport_type ORDER BY name ASC");
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
header('Content-Type: application/json');
echo json_encode($data);

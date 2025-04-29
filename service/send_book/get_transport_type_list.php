<?php
require '../connect.php'; // หรือ service/connect.php ตามโครงสร้างโปรเจกต์

$sql = "SELECT id, name FROM transport_type ORDER BY name ASC";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'id' => $row['id'],
        'name' => $row['name']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);

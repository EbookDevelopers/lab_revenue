<?php
require '../connect.php';

$sql = "
    SELECT 
        p.id,
        p.nap_number,
        p.hn,
        p.age,
        p.sex,
        h.name AS hospital
    FROM patients p
    LEFT JOIN hospital h ON p.hospital_id = h.id
    WHERE p.is_deleted = 0
    ORDER BY p.created_at DESC
";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);

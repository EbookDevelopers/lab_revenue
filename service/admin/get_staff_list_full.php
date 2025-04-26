<?php
require '../connect.php';

$sql = "
  SELECT s.id, CONCAT(s.fname, ' ', s.lname) AS fullname, s.phone, s.email, 
         d.name AS department, u.name AS duty, s.is_approved
  FROM staff s
  LEFT JOIN department d ON s.department_id = d.id
  LEFT JOIN duty u ON s.duty_id = u.id
  ORDER BY s.id DESC
";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
  $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);

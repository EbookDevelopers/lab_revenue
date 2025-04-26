<?php
require '../connect.php';

$start = $_GET['start'] ?? date('Y-m-01');
$end = $_GET['end'] ?? date('Y-m-d');

$sql = "
  SELECT d.name AS department, COUNT(*) AS total
  FROM login_logs l
  LEFT JOIN staff s ON l.staff_id = s.id
  LEFT JOIN department d ON s.department_id = d.id
  WHERE l.action = 'login' AND DATE(log_time) BETWEEN ? AND ?
  GROUP BY d.name
  ORDER BY total DESC
  LIMIT 5
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start, $end);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while($row = $result->fetch_assoc()) {
  $data[] = $row;
}

echo json_encode($data);

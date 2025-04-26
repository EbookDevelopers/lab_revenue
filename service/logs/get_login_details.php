<?php
require '../connect.php';

$start = $_GET['start'] ?? date('Y-m-01');
$end = $_GET['end'] ?? date('Y-m-d');
$department_id = $_GET['department_id'] ?? '';

$where = "WHERE l.action = 'login' AND DATE(log_time) BETWEEN ? AND ?";
$params = [$start, $end];
$types = "ss";

if ($department_id) {
  $where .= " AND s.department_id = ?";
  $params[] = $department_id;
  $types .= "i";
}

$sql = "
  SELECT CONCAT(s.fname, ' ', s.lname) AS name, d.name AS department, log_time, ip_address
  FROM login_logs l
  LEFT JOIN staff s ON l.staff_id = s.id
  LEFT JOIN department d ON s.department_id = d.id
  $where
  ORDER BY log_time DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($row = $res->fetch_assoc()) {
  $data[] = $row;
}
echo json_encode(['data' => $data]);

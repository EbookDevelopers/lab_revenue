<?php
require '../connect.php';

// กำหนดค่าเริ่มต้นของ $start และ $end จาก $_GET
$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';

$department_id = $_GET['department_id'] ?? '';
$where = "WHERE action = 'login' AND DATE(log_time) BETWEEN ? AND ?";
$params = [$start, $end];
$types = "ss";

if ($department_id) {
  $where .= " AND s.department_id = ?";
  $params[] = $department_id;
  $types .= "i";
}

$sql = "
  SELECT DATE(log_time) AS login_date, d.name AS department, COUNT(*) AS total
  FROM login_logs l
  LEFT JOIN staff s ON l.staff_id = s.id
  LEFT JOIN department d ON s.department_id = d.id
  $where
  GROUP BY login_date, d.name
  ORDER BY login_date ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($row = $res->fetch_assoc()) {
  $date = $row['login_date'];
  $dept = $row['department'] ?? 'ไม่ระบุ';
  $data[$dept][$date] = $row['total'];
}

echo json_encode($data);

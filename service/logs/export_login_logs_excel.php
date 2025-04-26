<?php
require '../connect.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$start = $_GET['start'] ?? date('Y-m-01');
$end = $_GET['end'] ?? date('Y-m-d');

$sql = "
  SELECT DATE(log_time) AS login_date, d.name AS department, COUNT(*) AS total
  FROM login_logs l
  LEFT JOIN staff s ON l.staff_id = s.id
  LEFT JOIN department d ON s.department_id = d.id
  WHERE action = 'login' AND DATE(log_time) BETWEEN ? AND ?
  GROUP BY login_date, d.name
  ORDER BY login_date ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start, $end);
$stmt->execute();
$res = $stmt->get_result();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->fromArray(['วันที่', 'กลุ่มงาน', 'จำนวนเข้าใช้งาน'], NULL, 'A1');

$rowIndex = 2;
while ($row = $res->fetch_assoc()) {
  $sheet->fromArray([$row['login_date'], $row['department'] ?? 'ไม่ระบุ', $row['total']], NULL, 'A'.$rowIndex++);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="login_logs.xlsx"');
$writer = new Xlsx($spreadsheet);
$writer->save("php://output");

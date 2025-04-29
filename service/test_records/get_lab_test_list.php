<?php
require '../connect.php';

// รับตัวแปรกรอง
$startMonth = $_GET['startMonth'] ?? '';
$endMonth = $_GET['endMonth'] ?? '';
$hospital_id = $_GET['hospital_id'] ?? '';

// สร้างเงื่อนไข WHERE
$where = "WHERE l.is_deleted = 0";

if ($startMonth && $endMonth) {
    $startDate = $startMonth . "-01";
    $endDate = $endMonth . "-31";
    $where .= " AND l.received_date BETWEEN '$startDate' AND '$endDate'";
}

if ($hospital_id) {
    $where .= " AND p.hospital_id = '".intval($hospital_id)."'";
}

$sql = "
    SELECT 
        l.lab_number,
        p.nap_number,
        l.cd4_number,
        l.hn,
        DATE_FORMAT(l.received_date, '%d/%m/%Y') AS received_date,
        DATE_FORMAT(l.analysis_date, '%d/%m/%Y') AS analysis_date,
        l.cd4,
        l.abs_cd4,
        l.wbc,
        s.name AS situation_name
    FROM lab_test l
    INNER JOIN patients p ON l.patient_id = p.id
    INNER JOIN situation s ON l.situation_id = s.id
    $where
    ORDER BY l.id DESC
";

// DataTables server-side
$res = $conn->query($sql);

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

$response = [
  "data" => $data
];

header('Content-Type: application/json');
echo json_encode($response);

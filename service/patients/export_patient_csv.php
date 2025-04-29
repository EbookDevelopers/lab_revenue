<?php
require '../connect.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="patient_list.csv"');

$output = fopen('php://output', 'w');

// หัวตาราง
fputcsv($output, ['NAP Number', 'HN', 'อายุ', 'เพศ', 'โรงพยาบาล']);

// ดึงข้อมูลจากฐานข้อมูล
$sql = "
    SELECT p.nap_number, p.hn, p.age, p.sex, h.name AS hospital
    FROM patients p
    LEFT JOIN hospital h ON p.hospital_id = h.id
    WHERE p.is_deleted = 0
    ORDER BY p.created_at DESC
";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['nap_number'],
        $row['hn'],
        $row['age'],
        $row['sex'],
        $row['hospital']
    ]);
}

fclose($output);
exit;

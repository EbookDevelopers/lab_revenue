<?php
require '../connect.php';
require 'vendor/autoload.php'; // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];

$sql = "
    SELECT p.nap_number, p.hn, p.age, p.sex, h.name AS hospital
    FROM patients p
    LEFT JOIN hospital h ON p.hospital_id = h.id
    WHERE p.is_deleted = 0
    AND DATE(p.created_at) BETWEEN ? AND ?
    ORDER BY p.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->fromArray(['NAP Number', 'HN', 'Age', 'Sex', 'Hospital'], NULL, 'A1');

$row = 2;
while ($r = $result->fetch_assoc()) {
    $sheet->fromArray(array_values($r), NULL, "A$row");
    $row++;
}

$filename = "patient_list_{$start_date}_to_{$end_date}.xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

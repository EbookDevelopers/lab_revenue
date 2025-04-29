<?php
require '../../service/connect.php';
require '../../plugins/phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// ตั้งหัวตาราง
$sheet->setCellValue('A1', 'ลำดับ');
$sheet->setCellValue('B1', 'โรงพยาบาล');
$sheet->setCellValue('C1', 'เลขที่หนังสือ');
$sheet->setCellValue('D1', 'วันที่ออกหนังสือ');
$sheet->setCellValue('E1', 'ประเภทสิ่งส่งตรวจ');
$sheet->setCellValue('F1', 'จำนวนตัวอย่าง');
$sheet->setCellValue('G1', 'สถานะ');

$sql = "
  SELECT sb.id, h.name AS hospital_name, sb.book_number, sb.book_date, s.name AS specimen_name, sb.sample_quantity, sb.status
  FROM send_book sb
  INNER JOIN hospital h ON sb.hospital_id = h.id
  INNER JOIN specimen s ON sb.specimen_id = s.id
  WHERE sb.is_deleted = 0
  ORDER BY sb.id DESC
";
$result = $conn->query($sql);
$row = 2;

while ($data = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $row, $data['id']);
    $sheet->setCellValue('B' . $row, $data['hospital_name']);
    $sheet->setCellValue('C' . $row, $data['book_number']);
    $sheet->setCellValue('D' . $row, date('d/m/Y', strtotime($data['book_date'])));
    $sheet->setCellValue('E' . $row, $data['specimen_name']);
    $sheet->setCellValue('F' . $row, $data['sample_quantity']);
    $sheet->setCellValue('G' . $row, $data['status']);
    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="send_books.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

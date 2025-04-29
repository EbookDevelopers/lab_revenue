<?php
require '../connect.php';
require('fpdf/fpdf.php');

$sql = "
    SELECT p.nap_number, p.hn, p.age, p.sex, h.name AS hospital
    FROM patients p
    LEFT JOIN hospital h ON p.hospital_id = h.id
    WHERE p.is_deleted = 0
    ORDER BY p.created_at DESC
";
$result = $conn->query($sql);

// เริ่มสร้าง PDF
$pdf = new FPDF();
$pdf->AddPage('L', 'A4');
$pdf->SetFont('Arial', 'B', 14);

$pdf->Cell(280, 10, iconv('UTF-8', 'TIS-620', 'รายชื่อผู้ป่วยที่เข้าตรวจเชื้อ'), 0, 1, 'C');
$pdf->Ln(5);

// หัวตาราง
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 10, iconv('UTF-8', 'TIS-620', 'NAP Number'), 1);
$pdf->Cell(40, 10, iconv('UTF-8', 'TIS-620', 'HN'), 1);
$pdf->Cell(20, 10, iconv('UTF-8', 'TIS-620', 'อายุ'), 1);
$pdf->Cell(30, 10, iconv('UTF-8', 'TIS-620', 'เพศ'), 1);
$pdf->Cell(120, 10, iconv('UTF-8', 'TIS-620', 'โรงพยาบาล'), 1);
$pdf->Ln();

// ข้อมูล
$pdf->SetFont('Arial', '', 12);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(50, 10, iconv('UTF-8', 'TIS-620', $row['nap_number']), 1);
    $pdf->Cell(40, 10, iconv('UTF-8', 'TIS-620', $row['hn']), 1);
    $pdf->Cell(20, 10, iconv('UTF-8', 'TIS-620', $row['age']), 1);
    $pdf->Cell(30, 10, iconv('UTF-8', 'TIS-620', $row['sex']), 1);
    $pdf->Cell(120, 10, iconv('UTF-8', 'TIS-620', $row['hospital']), 1);
    $pdf->Ln();
}

$pdf->Output('I', 'patient_list.pdf');
exit;

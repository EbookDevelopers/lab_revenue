<?php
require '../../service/connect.php';
require '../../plugins/fpdf/fpdf.php'; // สมมติใช้ FPDF

$id = $_GET['id'] ?? null;
if (!$id) {
    exit('ไม่พบรหัสหนังสือ');
}

$stmt = $conn->prepare("
    SELECT sb.*, h.name AS hospital_name, s.name AS specimen_name
    FROM send_book sb
    INNER JOIN hospital h ON sb.hospital_id = h.id
    INNER JOIN specimen s ON sb.specimen_id = s.id
    WHERE sb.id = ?
");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$book = $res->fetch_assoc();

if (!$book) {
    exit('ไม่พบข้อมูล');
}

// สร้าง PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

$pdf->Cell(0, 10, 'รายละเอียดหนังสือส่งตรวจ', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'โรงพยาบาล: ' . $book['hospital_name'], 0, 1);
$pdf->Cell(0, 10, 'เลขที่หนังสือ: ' . $book['book_number'], 0, 1);
$pdf->Cell(0, 10, 'วันที่ออกหนังสือ: ' . date('d/m/Y', strtotime($book['book_date'])), 0, 1);
$pdf->Cell(0, 10, 'ประเภทสิ่งส่งตรวจ: ' . $book['specimen_name'], 0, 1);
$pdf->Cell(0, 10, 'จำนวนตัวอย่าง: ' . $book['sample_quantity'], 0, 1);

$pdf->Output();
exit;

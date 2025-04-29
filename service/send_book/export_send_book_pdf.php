<?php
require '../../service/connect.php';
require '../../plugins/fpdf/fpdf.php'; // เรียกใช้ FPDF

class PDF extends FPDF
{
    function Header()
    {
        // ใส่หัวเอกสาร
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'รายการหนังสือส่งตรวจ', 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        // ใส่เลขหน้า
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'หน้า ' . $this->PageNo(), 0, 0, 'C');
    }

    function RotatedText($x, $y, $txt, $angle)
    {
        // หมุนข้อความเป็นลายน้ำ
        $this->Rotate($angle, $x, $y);
        $this->Text($x, $y, $txt);
        $this->Rotate(0);
    }

    function Rotate($angle, $x = -1, $y = -1)
    {
        if ($x == -1)
            $x = $this->x;
        if ($y == -1)
            $y = $this->y;
        if ($this->angle != 0)
            $this->_out('Q');
        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy));
        }
    }
}

$pdf = new PDF();
$pdf->AddPage('L', 'A4'); // Landscape แนวนอน
$pdf->SetFont('Arial', 'B', 12);

// วาดตารางหัว
$pdf->Cell(10, 10, '#', 1, 0, 'C');
$pdf->Cell(40, 10, 'โรงพยาบาล', 1, 0, 'C');
$pdf->Cell(35, 10, 'เลขที่หนังสือ', 1, 0, 'C');
$pdf->Cell(30, 10, 'วันที่ออกหนังสือ', 1, 0, 'C');
$pdf->Cell(45, 10, 'ประเภทสิ่งส่งตรวจ', 1, 0, 'C');
$pdf->Cell(25, 10, 'จำนวน', 1, 0, 'C');
$pdf->Cell(30, 10, 'สถานะ', 1, 1, 'C');

$pdf->SetFont('Arial', '', 12);

$sql = "
    SELECT sb.id, h.name AS hospital_name, sb.book_number, sb.book_date, s.name AS specimen_name, sb.sample_quantity, sb.status
    FROM send_book sb
    INNER JOIN hospital h ON sb.hospital_id = h.id
    INNER JOIN specimen s ON sb.specimen_id = s.id
    WHERE sb.is_deleted = 0
    ORDER BY sb.id DESC
";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(10, 10, $row['id'], 1, 0, 'C');
    $pdf->Cell(40, 10, iconv('UTF-8', 'TIS-620', $row['hospital_name']), 1, 0);
    $pdf->Cell(35, 10, iconv('UTF-8', 'TIS-620', $row['book_number']), 1, 0);
    $pdf->Cell(30, 10, date('d/m/Y', strtotime($row['book_date'])), 1, 0, 'C');
    $pdf->Cell(45, 10, iconv('UTF-8', 'TIS-620', $row['specimen_name']), 1, 0);
    $pdf->Cell(25, 10, $row['sample_quantity'], 1, 0, 'C');
    $pdf->Cell(30, 10, iconv('UTF-8', 'TIS-620', $row['status']), 1, 1, 'C');
}

// ใส่ลายน้ำกลางหน้า
$pdf->SetFont('Arial', 'B', 60);
$pdf->SetTextColor(240, 240, 240);
$pdf->RotatedText(70, 150, 'CONFIDENTIAL', 45);

$pdf->Output('I', 'send_book_list.pdf');

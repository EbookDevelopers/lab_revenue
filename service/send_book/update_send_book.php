<?php
require '../connect.php';

$id = intval($_POST['id'] ?? 0);
$book_number = $_POST['book_number'] ?? '';
$book_date = $_POST['book_date'] ?? '';
$sample_quantity = $_POST['sample_quantity'] ?? '';
$temperature = $_POST['temperature'] ?? '';

// ตรวจสอบเบื้องต้น
if ($id <= 0 || empty($book_number) || empty($book_date)) {
    http_response_code(400);
    echo 'ข้อมูลไม่ครบถ้วน';
    exit;
}

$stmt = $conn->prepare("
    UPDATE send_book 
    SET book_number = ?, book_date = ?, sample_quantity = ?, temperature = ? 
    WHERE id = ?
");
$stmt->bind_param('ssisi', $book_number, $book_date, $sample_quantity, $temperature, $id);

if ($stmt->execute()) {
    echo 'success';
} else {
    http_response_code(500);
    echo 'เกิดข้อผิดพลาดในการบันทึก';
}
?>

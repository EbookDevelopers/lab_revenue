<?php
session_start();
require '../../service/connect.php';

if (!isset($_SESSION['staff_id'])) {
    http_response_code(403);
    exit('ไม่ได้รับอนุญาต');
}

$hospital_id = $_POST['hospital_id'];
$book_number = $_POST['book_number'];
$book_date = $_POST['book_date'];
$send_date = $_POST['send_date'];
$received_datetime = $_POST['received_datetime'];
$specimen_id = $_POST['specimen_id'];
$sample_quantity = $_POST['sample_quantity'];
$sample_temperature = $_POST['sample_temperature'];
$transporter = $_POST['transporter'];
$sample_condition = $_POST['sample_condition'];
$created_by = $_SESSION['staff_id'];
$transporter_id = $_POST['transporter_id'] ?? null;

// อัปโหลดรูป
$upload_dir = '../../uploads/send_books/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$file = $_FILES['book_image'];
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$new_filename = uniqid('book_', true) . '.' . strtolower($ext);
$upload_path = $upload_dir . $new_filename;

if (move_uploaded_file($file['tmp_name'], $upload_path)) {
    $stmt = $conn->prepare("
        INSERT INTO send_book
        (hospital_id, book_number, book_date, send_date, received_datetime, book_image, specimen_id, sample_quantity, sample_temperature, transporter_id, sample_condition, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        'isssssiiisss',
        $hospital_id,
        $book_number,
        $book_date,
        $send_date,
        $received_datetime,
        $new_filename, // เก็บแค่ชื่อไฟล์
        $specimen_id,
        $sample_quantity,
        $sample_temperature,
        $transporter_id,
        $sample_condition,
        $created_by
    );

    if ($stmt->execute()) {
        header('Location: /lab_revenue/send_book');
        exit;
    } else {
        echo "ผิดพลาดในการบันทึกข้อมูล";
    }
} else {
    echo "อัปโหลดไฟล์ไม่สำเร็จ";
}

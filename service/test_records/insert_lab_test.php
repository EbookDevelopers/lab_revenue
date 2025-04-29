<?php
session_start();
require '../connect.php';

$patient_id = $_POST['patient_id'];
$cd4_number = $_POST['cd4_number'];
$analysis_date = $_POST['analysis_date'];
$wbc = $_POST['wbc'];
$lymp = $_POST['lymp'];
$cd4 = $_POST['cd4'];
$abs_cd4 = $_POST['abs_cd4'];
$situation_id = $_POST['situation_id'];
$hn = $_POST['hn'];
$send_book_id = intval($_POST['send_book_id'] ?? 0);

$staff_id = $_SESSION['staff_id'] ?? 1;

// สร้าง lab_number auto
$year_prefix = (date('Y') + 543) % 100; // ปี พ.ศ. เช่น 67
$stmt = $conn->prepare("SELECT lab_number FROM lab_test WHERE lab_number LIKE ? ORDER BY lab_number DESC LIMIT 1");
$search_prefix = $year_prefix . '%';
$stmt->bind_param('s', $search_prefix);
$stmt->execute();
$res = $stmt->get_result();
$last = $res->fetch_assoc();

if ($last) {
    $last_number = (int) substr($last['lab_number'], 2); // ตัดเอาเฉพาะ running
    $new_running = str_pad($last_number + 1, 5, '0', STR_PAD_LEFT);
} else {
    $new_running = '00001';
}
$lab_number = $year_prefix . $new_running;

$stmt = $conn->prepare("
    INSERT INTO lab_test 
    (lab_number, send_book_id, patient_id, cd4_number, received_date, analysis_date, wbc, lymp, cd4, abs_cd4, situation_id, created_by,hn)
    VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, ?, ?, ?, ?, ?, ?, ?,?)
");
$stmt->bind_param('siissdddisis', $lab_number, $patient_id, $cd4_number, $analysis_date, $wbc, $lymp, $cd4, $abs_cd4, $situation_id, $staff_id, $hn);

if ($stmt->execute()) {
    echo "บันทึกข้อมูลเรียบร้อยแล้ว (Lab Number: $lab_number)";
} else {
    http_response_code(500);
    echo "เกิดข้อผิดพลาด";
}

<?php
session_start();
require '../connect.php';
require 'vendor/autoload.php'; // ใช้ PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

if (!isset($_FILES['patient_file'])) {
    http_response_code(400);
    echo "ไม่ได้เลือกไฟล์";
    exit;
}

$fileTmp = $_FILES['patient_file']['tmp_name'];

try {
    $spreadsheet = IOFactory::load($fileTmp);
} catch (Exception $e) {
    http_response_code(400);
    echo "ไม่สามารถอ่านไฟล์ได้: " . $e->getMessage();
    exit;
}

$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, true, true); // อ่านแบบคงตำแหน่งคอลัมน์
$errors = [];
$success = 0;

// สมมุติว่าแถวที่ 1 เป็นหัวตาราง
foreach ($rows as $index => $row) {
    if ($index == 1) continue; // ข้ามหัวตาราง

    $nap_number = trim($row['A']);
    $hospital_code = trim($row['B']);
    $hn = trim($row['C']);
    $age = (int) $row['D'];
    $sex = trim($row['E']);

    // ตรวจสอบ hospital code
    $stmt = $conn->prepare("SELECT id FROM hospital WHERE code = ?");
    $stmt->bind_param('s', $hospital_code);
    $stmt->execute();
    $res = $stmt->get_result();
    $hospital = $res->fetch_assoc();

    if (!$hospital) {
        $errors[] = "แถวที่ {$index} ➔ Hospital Code ไม่ถูกต้อง: $hospital_code";
        continue;
    }

    $hospital_id = $hospital['id'];

    // ตรวจสอบ HN ซ้ำ
    $stmt = $conn->prepare("SELECT id FROM patients WHERE hn = ? AND hospital_id = ? AND is_deleted = 0");
    $stmt->bind_param('si', $hn, $hospital_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $errors[] = "แถวที่ {$index} ➔ พบ HN ซ้ำ: $hn ในโรงพยาบาล [$hospital_code]";
        continue;
    }

    // Insert ข้อมูล
    $created_by = $_SESSION['staff_id'] ?? 1; // ใส่ admin id ถ้าไม่มี staff
    $stmt = $conn->prepare("
        INSERT INTO patients (nap_number, hospital_id, hn, age, sex, created_by)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('sisssi', $nap_number, $hospital_id, $hn, $age, $sex, $created_by);

    if ($stmt->execute()) {
        $success++;
    } else {
        $errors[] = "แถวที่ {$index} ➔ เกิดข้อผิดพลาดบันทึกข้อมูล HN: $hn";
    }
}

if (count($errors) > 0) {
    http_response_code(400);
    echo "นำเข้าสำเร็จ {$success} รายการ มีข้อผิดพลาด ".count($errors)." รายการ:\n" . implode("\n", $errors);
} else {
    echo "นำเข้าสำเร็จ {$success} รายการทั้งหมด!";
}

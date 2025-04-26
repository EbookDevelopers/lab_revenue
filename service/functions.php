<?php

// 🔐 เข้ารหัสรหัสผ่าน (ไม่จำเป็นถ้าใช้ password_hash โดยตรง)
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// ✅ ตรวจสอบรหัสผ่าน
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// 🔢 สร้างรหัสสุ่ม เช่น uid, token
function generateUID($prefix = 'staff_', $length = 6) {
    $rand = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
    return $prefix . $rand;
}

// 🗓️ แสดงวันที่ไทยแบบเต็ม
function thaiDate($date) {
    $months = [
        "", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.",
        "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."
    ];
    $timestamp = strtotime($date);
    $day = date("j", $timestamp);
    $month = $months[date("n", $timestamp)];
    $year = date("Y", $timestamp) + 543;
    return "$day $month $year";
}

?>

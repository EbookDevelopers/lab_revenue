<?php
require '../connect.php'; // ไฟล์เชื่อมต่อ DB
//require '../functions.php'; // ฟังก์ชันสำหรับ hash password ฯลฯ

$fname = $_POST['fname'];
$lname = $_POST['lname'];
$department = $_POST['department'];
$duty = $_POST['duty'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$permission = $_POST['permission'];
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$uid = uniqid('staff_');

// ยังไม่ approved ตอนแรก
$is_approved = 0;

$sql = "INSERT INTO staff (uid, fname, lname, department_id, duty_id, phone, email, permission_id, username, password, is_approved) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssssi", $uid, $fname, $lname, $department, $duty, $phone, $email, $permission, $username, $password, $is_approved);

if ($stmt->execute()) {
  echo "success";
} else {
  http_response_code(500);
  echo "error";
}
?>

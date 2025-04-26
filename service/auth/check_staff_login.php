<?php
session_start();
require '../connect.php';

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM staff WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();

if ($staff && password_verify($password, $staff['password'])) {
    $_SESSION['staff_id'] = $staff['id'];
    $_SESSION['staff_name'] = $staff['fname'] . ' ' . $staff['lname'];
    $_SESSION['staff_permission_id'] = $staff['permission_id']; // เก็บสิทธิ์ไว้ใน session

    // ✅ Insert login log with timestamp
    $stmtLog = $conn->prepare("
        INSERT INTO login_logs (staff_id, action, ip_address, user_agent, log_time)
        VALUES (?, 'login', ?, ?, NOW())
    ");
    $ip = $_SERVER['REMOTE_ADDR'];
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $stmtLog->bind_param("iss", $staff['id'], $ip, $agent);
    $stmtLog->execute();

    echo 'success';
} else {
    echo 'fail';
}

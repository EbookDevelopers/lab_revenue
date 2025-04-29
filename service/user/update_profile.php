<?php
session_start();
require '../connect.php';

$id = $_POST['id'] ?? '';
$fname = trim($_POST['fname']);
$lname = trim($_POST['lname']);
$tel = trim($_POST['tel']);
$email = trim($_POST['email']);

if ($id != $_SESSION['staff_id']) {
    http_response_code(403);
    exit('Permission denied');
}

$stmt = $conn->prepare("
    UPDATE staff 
    SET fname = ?, lname = ?, phone = ?, email = ?
    WHERE id = ?
");
$stmt->bind_param('ssssi', $fname, $lname, $tel, $email, $id);

if ($stmt->execute()) {
    echo 'success';
}

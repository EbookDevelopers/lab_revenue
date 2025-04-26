<?php
require '../connect.php';

$id = $_POST['id'];
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$phone = $_POST['phone'];
$email = $_POST['email'];

$stmt = $conn->prepare("UPDATE staff SET fname = ?, lname = ?, phone = ?, email = ? WHERE id = ?");
$stmt->bind_param("ssssi", $fname, $lname, $phone, $email, $id);

if ($stmt->execute()) {
  echo 'success';
} else {
  http_response_code(500);
  echo 'fail';
}

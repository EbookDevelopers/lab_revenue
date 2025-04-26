<?php
require '../connect.php';

$id = $_POST['id'];
$sql = "UPDATE staff SET is_approved = 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
  echo 'success';
} else {
  echo 'fail';
}

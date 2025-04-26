<?php
require '../connect.php';

$id = $_POST['id'];

$stmt = $conn->prepare("DELETE FROM staff WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
  echo 'success';
} else {
  http_response_code(500);
  echo 'fail';
}

<?php
require '../connect.php';

$id = $_POST['id'];

$stmt = $conn->prepare("
    UPDATE patients 
    SET is_deleted = 1
    WHERE id = ?
");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo 'success';
} else {
    http_response_code(500);
    echo 'error';
}

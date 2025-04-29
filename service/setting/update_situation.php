<?php
require '../connect.php';

$id = $_POST['id'] ?? '';
$edit_name = trim($_POST['edit_name'] ?? '');

if (!$id || $edit_name === '') {
    http_response_code(400);
    echo 'invalid';
    exit;
}

$stmt = $conn->prepare("UPDATE situation SET name = ? WHERE id = ?");
$stmt->bind_param('si', $edit_name, $id);

if ($stmt->execute()) {
    echo 'success';
} else {
    http_response_code(500);
    echo 'error';
}

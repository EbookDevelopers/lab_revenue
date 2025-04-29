<?php
require '../connect.php';

$id = $_POST['id'] ?? '';

if (!$id) {
    http_response_code(400);
    echo 'invalid id';
    exit;
}

// ลบจริง (DELETE FROM)
$stmt = $conn->prepare("DELETE FROM situation WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    echo 'success';
} else {
    http_response_code(500);
    echo 'error';
}

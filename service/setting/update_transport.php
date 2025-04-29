<?php
require '../connect.php';

$id = intval($_POST['id'] ?? 0);
$name = trim($_POST['edit_name'] ?? '');

if ($id > 0 && $name !== '') {
    $stmt = $conn->prepare("UPDATE transport_type SET name = ? WHERE id = ?");
    $stmt->bind_param('si', $name, $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        http_response_code(500);
        echo 'error';
    }
} else {
    http_response_code(400);
    echo 'Invalid data';
}

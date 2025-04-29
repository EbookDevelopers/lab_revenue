<?php
require '../connect.php';

$name = trim($_POST['name'] ?? '');

if ($name !== '') {
    $stmt = $conn->prepare("INSERT INTO transport_type (name) VALUES (?)");
    $stmt->bind_param('s', $name);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        http_response_code(500);
        echo 'error';
    }
} else {
    http_response_code(400);
    echo 'Name is required';
}

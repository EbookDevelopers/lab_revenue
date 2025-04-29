<?php
require '../connect.php';

$situation_name = trim($_POST['situation_name'] ?? '');

if ($situation_name === '') {
    http_response_code(400);
    echo 'invalid';
    exit;
}

$stmt = $conn->prepare("INSERT INTO situation (name) VALUES (?)");
$stmt->bind_param('s', $situation_name);

if ($stmt->execute()) {
    echo 'success';
} else {
    http_response_code(500);
    echo 'error';
}

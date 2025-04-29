<?php
require '../connect.php';

$id = $_GET['id'];

$stmt = $conn->prepare("
    SELECT id, nap_number, hn, age, sex
    FROM patients
    WHERE id = ? AND is_deleted = 0
");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($data);

<?php
require '../connect.php';

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM staff WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($data);

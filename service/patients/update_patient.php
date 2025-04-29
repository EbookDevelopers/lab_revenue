<?php
session_start();
require '../connect.php';

$id = $_POST['id'];
$nap_number = $_POST['nap_number'];
$hn = $_POST['hn'];
$age = $_POST['age'];
$sex = $_POST['sex'];
$updated_by = $_SESSION['staff_id'];

$stmt = $conn->prepare("
    UPDATE patients 
    SET nap_number = ?, hn = ?, age = ?, sex = ?, updated_by = ?, updated_at = NOW()
    WHERE id = ? AND is_deleted = 0
");
$stmt->bind_param("ssisii", $nap_number, $hn, $age, $sex, $updated_by, $id);

if ($stmt->execute()) {
    echo 'success';
} else {
    http_response_code(500);
    echo 'error';
}

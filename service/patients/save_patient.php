<?php
session_start();
require '../connect.php';

$nap_number = $_POST['nap_number'];
$hospital_id = $_POST['hospital_id'];
$hn = $_POST['hn'];
$age = $_POST['age'];
$sex = $_POST['sex'];
$created_by = $_SESSION['staff_id'];

try {
    $stmt = $conn->prepare("
        INSERT INTO patients (nap_number, hospital_id, hn, age, sex, created_by)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sisssi", $nap_number, $hospital_id, $hn, $age, $sex, $created_by);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        http_response_code(500);
        echo 'error';
    }
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo 'error';
}

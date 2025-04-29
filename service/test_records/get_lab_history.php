<?php
require '../connect.php'; // หรือปรับ path ตามโปรเจกต์คุณ

$patient_id = intval($_GET['patient_id'] ?? 0);

if ($patient_id > 0) {
    $stmt = $conn->prepare("
        SELECT
            lab_number,
            DATE_FORMAT(received_date, '%d/%m/%Y') AS received_date,
            DATE_FORMAT(analysis_date, '%d/%m/%Y') AS analysis_date,
            wbc,
            lymp,
            cd4,
            abs_cd4
        FROM lab_test
        WHERE patient_id = ?
        ORDER BY analysis_date DESC
    ");
    $stmt->bind_param('i', $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
} else {
    // กรณีไม่มี patient_id ที่ส่งมา
    echo json_encode([]);
}
?>

<?php
require '../connect.php';

// 7 วันย้อนหลัง
$labels = [];
$data7 = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $labels[] = $day;
    $res = $conn->query("SELECT COUNT(*) AS total FROM login_logs WHERE action='login' AND DATE(log_time) = '$day'");
    $data7[] = (int) $res->fetch_assoc()['total'];
}

// TOP 5 แผนก
$sql = "SELECT d.name AS dept, COUNT(*) AS total 
        FROM login_logs l 
        LEFT JOIN staff s ON l.staff_id = s.id 
        LEFT JOIN department d ON s.department_id = d.id 
        WHERE action='login'
        GROUP BY d.name ORDER BY total DESC LIMIT 5";
$result = $conn->query($sql);
$top5 = ['labels' => [], 'data' => []];
while ($r = $result->fetch_assoc()) {
    $top5['labels'][] = $r['dept'] ?? 'ไม่ระบุ';
    $top5['data'][] = $r['total'];
}

echo json_encode([
    'last7days' => ['labels' => $labels, 'data' => $data7],
    'top5' => $top5
]);

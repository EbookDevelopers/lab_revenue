<?php
require '../connect.php';

$search = $_GET['search'] ?? '';

$stmt = $conn->prepare("
    SELECT id, CONCAT(book_number, ' (', DATE_FORMAT(book_date, '%d/%m/%Y'), ')') AS text
    FROM send_book
    WHERE book_number LIKE CONCAT('%', ?, '%')
    ORDER BY book_date DESC
    LIMIT 20
");
$stmt->bind_param('s', $search);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);

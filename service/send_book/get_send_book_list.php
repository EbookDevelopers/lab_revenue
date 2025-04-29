<?php
require '../../service/connect.php';

$sql = "
  SELECT sb.id, h.name AS hospital_name, sb.book_number, DATE_FORMAT(sb.book_date, '%d/%m/%Y') AS book_date,
         s.name AS specimen_name, sb.sample_quantity, sb.status, sb.book_image
  FROM send_book sb
  INNER JOIN hospital h ON sb.hospital_id = h.id
  INNER JOIN specimen s ON sb.specimen_id = s.id
  WHERE sb.is_deleted = 0
  ORDER BY sb.id DESC
";

$res = $conn->query($sql);
$data = [];

while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);

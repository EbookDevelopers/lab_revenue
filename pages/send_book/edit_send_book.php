<?php
session_start();
require '../../service/connect.php';

if (!isset($_SESSION['staff_id'])) {
    header('Location: /lab_revenue/login');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    die('ไม่พบรหัสหนังสือ');
}

// ดึงข้อมูลหนังสือที่ต้องการแก้ไข
$stmt = $conn->prepare("
    SELECT * FROM send_book 
    WHERE id = ? AND is_deleted = 0
");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$send_book = $result->fetch_assoc();

if (!$send_book) {
    die('ไม่พบข้อมูลหนังสือ');
}

// ดึงข้อมูลโรงพยาบาล
$hospitals = $conn->query("SELECT id, CONCAT(code, ' ', name) AS hosp_name FROM hospital ORDER BY name ASC");
// ดึงข้อมูล specimen
$specimens = $conn->query("SELECT id, name FROM specimen ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>แก้ไขหนังสือส่งตรวจ</title>
    <link rel="stylesheet" href="/lab_revenue/assets/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper p-4">

        <h2>✏️ แก้ไขหนังสือส่งตรวจ</h2>

        <form id="editSendBookForm" action="/lab_revenue/service/send_book/update_send_book.php" method="post">
            <input type="hidden" name="id" value="<?= htmlspecialchars($send_book['id']) ?>">

            <div class="row">
                <div class="col-md-4">
                    <label>โรงพยาบาล</label>
                    <select name="hospital_id" class="form-control" required>
                        <option value="">-- เลือกโรงพยาบาล --</option>
                        <?php while ($h = $hospitals->fetch_assoc()): ?>
                            <option value="<?= $h['id'] ?>" <?= ($h['id'] == $send_book['hospital_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($h['hosp_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>เลขที่หนังสือ</label>
                    <input type="text" name="book_number" class="form-control" value="<?= htmlspecialchars($send_book['book_number']) ?>" required>
                </div>

                <div class="col-md-4">
                    <label>วันที่ออกหนังสือ</label>
                    <input type="date" name="book_date" class="form-control" value="<?= $send_book['book_date'] ?>" required>
                </div>

                <div class="col-md-4 mt-3">
                    <label>วันที่ส่งตรวจ</label>
                    <input type="date" name="send_date" class="form-control" value="<?= $send_book['send_date'] ?>" required>
                </div>

                <div class="col-md-4 mt-3">
                    <label>วัน/เวลารับตัวอย่าง</label>
                    <input type="datetime-local" name="received_datetime" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($send_book['received_datetime'])) ?>" required>
                </div>

                <div class="col-md-4 mt-3">
                    <label>ประเภทสิ่งส่งตรวจ</label>
                    <select name="specimen_id" class="form-control" required>
                        <option value="">-- เลือก Specimen --</option>
                        <?php while ($s = $specimens->fetch_assoc()): ?>
                            <option value="<?= $s['id'] ?>" <?= ($s['id'] == $send_book['specimen_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-2 mt-3">
                    <label>จำนวนตัวอย่าง</label>
                    <input type="number" name="sample_quantity" class="form-control" value="<?= htmlspecialchars($send_book['sample_quantity']) ?>" required>
                </div>

                <div class="col-md-2 mt-3">
                    <label>อุณหภูมิสิ่งส่งตรวจ</label>
                    <input type="text" name="sample_temperature" class="form-control" value="<?= htmlspecialchars($send_book['sample_temperature']) ?>">
                </div>

                <div class="col-md-4 mt-3">
                    <label>ขนส่งโดย</label>
                    <input type="text" name="transporter" class="form-control" value="<?= htmlspecialchars($send_book['transporter']) ?>">
                </div>

                <div class="col-md-4 mt-3">
                    <label>สภาพสิ่งส่งตรวจ</label>
                    <input type="text" name="sample_condition" class="form-control" value="<?= htmlspecialchars($send_book['sample_condition']) ?>">
                </div>
            </div>

            <div class="text-right mt-4">
                <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
            </div>

        </form>

    </div>
    <script>
        $('#sendBookTable').on('click', '.restoreBtn', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'กู้คืนข้อมูล?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'กู้คืน',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('/lab_revenue/service/send_book/restore_send_book.php', {
                        id: id
                    }, function() {
                        Swal.fire('กู้คืนสำเร็จ', '', 'success');
                        table.ajax.reload();
                    }).fail(function() {
                        Swal.fire('ผิดพลาด', '', 'error');
                    });
                }
            });
        });
    </script>
</body>

</html>
<?php
require '../../service/connect.php';
require '../../service/auth/check_permission.php';
if (!isset($_SESSION['staff_id'])) {
    header("Location: /lab_revenue/login");
    exit;
}
checkPermission([1, 3, 5]);


// ดึงโรงพยาบาล
$hospitals = $conn->query("SELECT id, CONCAT(code, ' ', name) AS hosp_name FROM hospital ORDER BY name ASC");
// ดึง Specimen
$specimens = $conn->query("SELECT id, name FROM specimen ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <base href="/lab_revenue/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เพิ่มหนังสือส่งตรวจ</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico">

    <!-- Important Stylesheets -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/css/adminlte.min.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Sweet Alert -->
    <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include_once(__DIR__ . '/../includes/sidebar.php'); ?>
        <div class="content-wrapper p-3">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h2>📄 เพิ่มหนังสือส่งตรวจ</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/lab_revenue/dashboard">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">เพิ่มหนังสือส่งตรวจ</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <div class="content">
                <form id="sendBookForm" action="/lab_revenue/service/send_book/save_send_book.php" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4">
                            <label>โรงพยาบาล</label>
                            <select name="hospital_id" class="form-control" required>
                                <option value="">-- เลือกโรงพยาบาล --</option>
                                <?php while ($h = $hospitals->fetch_assoc()): ?>
                                    <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['hosp_name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>เลขที่หนังสือ</label>
                            <input type="text" name="book_number" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label>ไฟล์รูปภาพหนังสือ (jpg, png)</label>
                            <input type="file" name="book_image" class="form-control" accept="image/*" required>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label>วันที่ออกหนังสือ</label>
                            <input type="date" name="book_date" class="form-control" required>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label>วันที่ส่งตรวจ</label>
                            <input type="date" name="send_date" class="form-control" required>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label>วัน/เวลารับตัวอย่าง</label>
                            <input type="datetime-local" name="received_datetime" class="form-control" required>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label>ประเภทสิ่งส่งตรวจ</label>
                            <select name="specimen_id" class="form-control" required>
                                <option value="">-- เลือก Specimen --</option>
                                <?php while ($s = $specimens->fetch_assoc()): ?>
                                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-2 mt-3">
                            <label>จำนวนตัวอย่าง</label>
                            <input type="number" name="sample_quantity" class="form-control" required>
                        </div>

                        <div class="col-md-2 mt-3">
                            <label>อุณหภูมิสิ่งส่งตรวจ</label>
                            <input type="text" name="sample_temperature" class="form-control">
                        </div>

                        <div class="col-md-4 mt-3">
                            <label>ขนส่งโดย</label>
                            <input type="text" name="transporter" class="form-control">
                        </div>

                        <div class="col-md-4 mt-3">
                            <label>สภาพสิ่งส่งตรวจ</label>
                            <input type="text" name="sample_condition" class="form-control">
                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>
        <?php include_once(__DIR__ . '/../includes/footer.php'); ?>
    </div>

    <!-- Important Scripts -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/adminlte.min.js"></script>

    <!-- Sweet Alert -->
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#logoutBtn').click(function(e) {
                e.preventDefault(); // ป้องกันพฤติกรรมเริ่มต้นของ <a>
                Swal.fire({
                    title: 'ข้อความระบบ',
                    text: "คุณต้องการออกจากระบบหรือไม่?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/lab_revenue/logout', // URL rewrite-friendly
                            type: 'POST',
                            success: function(response) {
                                let res = JSON.parse(response);
                                if (res.status === 'success') {
                                    Swal.fire(
                                        'ออกจากระบบสำเร็จ',
                                        res.message,
                                        'success'
                                    ).then(() => {
                                        window.location.href = '/lab_revenue/login'; // URL rewrite ไป login.php
                                    });
                                } else {
                                    Swal.fire('ผิดพลาด', res.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('ผิดพลาด', 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>
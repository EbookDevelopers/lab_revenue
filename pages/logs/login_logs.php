<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: /lab_revenue/login');
    exit;
}
require '../../service/connect.php';
?>

<!DOCTYPE html>
<html>

<head>
    <base href="/lab_revenue/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Logs</title>
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

    <!-- jQuery DataTable -->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include_once(__DIR__ . '/../includes/sidebar.php'); ?>
        <div class="content-wrapper p-3">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h3>📄 ประวัติการเข้าใช้งาน</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/lab_revenue/admin_dashboard">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">ประวัติการเข้าใช้งาน</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <div class="content">
                <table class="table table-bordered" id="logsTable">
                    <thead>
                        <tr>
                            <th>ชื่อเจ้าหน้าที่</th>
                            <th>การกระทำ</th>
                            <th>เวลา</th>
                            <th>IP</th>
                            <th>Browser</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT l.*, CONCAT(s.fname, ' ', s.lname) AS staff_name 
              FROM login_logs l
              LEFT JOIN staff s ON l.staff_id = s.id
              ORDER BY l.log_time DESC";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?= $row['staff_name'] ?></td>
                                <td><?= strtoupper($row['action']) ?></td>
                                <td><?= $row['log_time'] ?></td>
                                <td><?= $row['ip_address'] ?></td>
                                <td><?= $row['user_agent'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php include_once(__DIR__ . '/../includes/footer.php'); ?>
    </div>

    <!-- Important Scripts -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/adminlte.min.js"></script>

    <!-- Local Script -->
    <script src="/lab_revenue/pages/admin/script.js"></script>

    <!-- JQuery DataTable -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>

    <!-- Sweet Alert -->
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
    <script>
        $(function() {
            $('#logsTable').DataTable();
        });

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
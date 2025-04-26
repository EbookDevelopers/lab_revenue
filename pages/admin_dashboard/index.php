<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: /lab_revenue/login");
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
    <title>Administrator Dashboard</title>
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
    <?php include_once(__DIR__ . '/../includes/sidebar.php'); ?>
    <div class="wrapper">     
        <!-- Content -->
        <div class="content-wrapper p-4">
            <h2>ยินดีต้อนรับ, <?= $_SESSION['admin_name'] ?> 👋</h2>
            <p>แดชบอร์ดผู้ดูแลระบบ</p>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>
                                <?php
                                $res = $conn->query("SELECT COUNT(*) AS total FROM staff WHERE is_approved = 0");
                                $data = $res->fetch_assoc();
                                echo $data['total'];
                                ?>
                            </h3>
                            <p>รอการอนุมัติ</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-clock"></i></div>
                        <a href="/lab_revenue/admin" class="small-box-footer">ตรวจสอบ <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>
                                <?php
                                $res = $conn->query("SELECT COUNT(*) AS total FROM staff WHERE is_approved = 1");
                                $data = $res->fetch_assoc();
                                echo $data['total'];
                                ?>
                            </h3>
                            <p>เจ้าหน้าที่ที่ได้รับอนุมัติแล้ว</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <!-- <a href="/lab_revenue/admin" class="small-box-footer">ดูทั้งหมด</a> -->
                    </div>
                </div>
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
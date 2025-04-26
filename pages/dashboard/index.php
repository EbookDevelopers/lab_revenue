<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header("Location: /lab_revenue/login");
    exit;
}
$is_admin = ($_SESSION['staff_permission_id'] == 1);
?>

<!DOCTYPE html>
<html>

<head>
    <base href="/lab_revenue/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
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
        <div class="content-wrapper p-4">
            <h2>🎉 ยินดีต้อนรับ <?= $_SESSION['staff_name'] ?></h2>

            <?php if ($is_admin): ?>
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>
                                    <?php
                                    $today = date('Y-m-d');
                                    $q = $conn->query("SELECT COUNT(*) AS total FROM login_logs WHERE action='login' AND DATE(log_time) = '$today'");
                                    echo $q->fetch_assoc()['total'];
                                    ?>
                                </h3>
                                <p>ผู้ใช้งานวันนี้</p>
                            </div>
                            <div class="icon"><i class="fas fa-user-clock"></i></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>
                                    <?php
                                    $month = date('Y-m');
                                    $q = $conn->query("SELECT COUNT(*) AS total FROM login_logs WHERE action='login' AND DATE_FORMAT(log_time, '%Y-%m') = '$month'");
                                    echo $q->fetch_assoc()['total'];
                                    ?>
                                </h3>
                                <p>เข้าสู่ระบบเดือนนี้</p>
                            </div>
                            <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <canvas id="login7daysChart" height="100"></canvas>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <canvas id="top5DeptChart" height="100"></canvas>
                    </div>
                    <div class="col-md-6 text-right mt-4">
                        <a href="/lab_revenue/login_stats" class="btn btn-outline-primary"><i class="fas fa-history"></i> ดูประวัติการเข้าใช้งานทั้งหมด</a>
                    </div>
                </div>
            <?php else: ?>
                <p>📋 เข้าถึงเมนูแบบฟอร์มและข้อมูลที่คุณได้รับอนุญาต</p>
            <?php endif; ?>
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

    <!-- ChartJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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

        $(function() {
            $.get('service/logs/api_login_summary.php', function(res) {
                const data = JSON.parse(res);

                // 7 วันย้อนหลัง
                const ctx = document.getElementById('login7daysChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.last7days.labels,
                        datasets: [{
                            label: 'จำนวนเข้าสู่ระบบ',
                            data: data.last7days.data,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            fill: false
                        }]
                    }
                });

                // TOP 5 แผนก
                const ctx2 = document.getElementById('top5DeptChart').getContext('2d');
                new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: data.top5.labels,
                        datasets: [{
                            label: 'จำนวนเข้าสู่ระบบ',
                            data: data.top5.data,
                            backgroundColor: 'rgba(54, 162, 235, 0.7)'
                        }]
                    }
                });
            });
        });
    </script>
</body>

</html>
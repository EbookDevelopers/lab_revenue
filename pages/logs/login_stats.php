<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: /lab_revenue/login');
    exit;
}
require '../../service/connect.php';
$departments = $conn->query("SELECT id, name FROM department ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>

<head>
    <base href="/lab_revenue/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>สถิติการ Login</title>
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
                            <h3>📈 สถิติการ Login รายวัน</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/lab_revenue/admin_dashboard">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">สถิติการ Login รายวัน</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <div class="content">
                <form id="filterForm" class="form-inline mb-4">
                    <label>เริ่ม: <input type="date" name="start" id="start" class="form-control mx-2" value="<?= date('Y-m-01') ?>"></label>
                    <label>ถึง: <input type="date" name="end" id="end" class="form-control mx-2" value="<?= date('Y-m-d') ?>"></label>
                    <label>กลุ่มงาน:
                        <select name="department_id" id="department_id" class="form-control mx-2">
                            <option value="">ทั้งหมด</option>
                            <?php while ($dept = $departments->fetch_assoc()): ?>
                                <option value="<?= $dept['id'] ?>"><?= $dept['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </label>

                    <button type="submit" class="btn btn-primary">แสดงข้อมูล</button>
                    <a href="#" id="exportExcel" class="btn btn-success ml-2">Export Excel</a>
                </form>

                <canvas id="loginChart" height="100"></canvas>

                <hr>
                <h4>Top 5 แผนกที่ Login บ่อยที่สุด</h4>
                <div class="row mb-4">
                    <div class="col-lg-6 col-sm-12">
                        <canvas id="barChart" height="100"></canvas>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <canvas id="pieChart" height="5" width="5" class="mt-4"></canvas>
                    </div>
                    <div class="row"></div>
                    <div class="row"></div>
                </div>

                <hr>
                <h4>📄 รายละเอียดการ Login</h4>
                <table id="loginDetailsTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ชื่อเจ้าหน้าที่</th>
                            <th>กลุ่มงาน</th>
                            <th>เวลาเข้าใช้งาน</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
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

    <!-- Sweet Alert -->
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>

    <!-- ChartJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- JQuery DataTable -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
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
        
        function loadLoginDetails(start, end, dept) {
            $('#loginDetailsTable').DataTable({
                destroy: true,
                ajax: {
                    url: 'service/logs/get_login_details.php',
                    data: {
                        start,
                        end,
                        department_id: dept
                    }
                },
                columns: [{
                        data: 'name'
                    },
                    {
                        data: 'department'
                    },
                    {
                        data: 'log_time'
                    },
                    {
                        data: 'ip_address'
                    }
                ]
            });
        }

        function reloadAllCharts() {
            const start = $('#start').val();
            const end = $('#end').val();
            const dept = $('#department_id').val();

            fetchChartData(start, end, dept);
            fetchTop5Charts(start, end, dept);
            loadLoginDetails(start, end, dept);
        }

        function fetchTop5Charts(start, end, dept) {
            $.get('service/logs/get_top5_department_logins.php', {
                start,
                end,
                department_id: dept
            }, function(res) {
                const data = JSON.parse(res);
                const labels = data.map(item => item.department ?? 'ไม่ระบุ');
                const counts = data.map(item => item.total);

                // Bar Chart
                const ctx1 = document.getElementById('barChart').getContext('2d');
                // ตรวจสอบว่า window.barChart มีอยู่และเป็นอินสแตนซ์ของ Chart ก่อนทำลาย
                if (window.barChart instanceof Chart) {
                    window.barChart.destroy();
                }
                window.barChart = new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'จำนวน Login',
                            data: counts,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'จำนวน Login ตามแผนก (Top 5)'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Pie Chart
                const ctx2 = document.getElementById('pieChart').getContext('2d');
                // ตรวจสอบว่า window.pieChart มีอยู่และเป็นอินสแตนซ์ของ Chart ก่อนทำลาย
                if (window.pieChart instanceof Chart) {
                    window.pieChart.destroy();
                }
                window.pieChart = new Chart(ctx2, {
                    type: 'pie',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Login Share',
                            data: counts,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'สัดส่วน Login ต่อแผนก (Top 5)'
                            }
                        }
                    }
                });
            });
        }


        function fetchChartData(start, end, dept) {
            $.get('service/logs/get_login_stats.php', {
                start,
                end,
                department_id: dept
            }, function(res) {
                const data = JSON.parse(res);
                const labels = [...new Set(Object.values(data).flatMap(obj => Object.keys(obj)))].sort();
                const datasets = [];

                Object.entries(data).forEach(([dept, logins]) => {
                    const deptData = labels.map(label => logins[label] || 0);
                    datasets.push({
                        label: dept,
                        data: deptData,
                        fill: false,
                        borderWidth: 2
                    });
                });

                const ctx = document.getElementById('loginChart').getContext('2d');
                // ตรวจสอบว่า window.pieChart มีอยู่และเป็นอินสแตนซ์ของ Chart ก่อนทำลาย
                if (window.loginChart instanceof Chart) {
                    window.loginChart.destroy();
                }
                window.loginChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'จำนวนผู้เข้าใช้งานรายวัน แยกตามกลุ่มงาน'
                            }
                        }
                    }
                });
            });
        }

        $(function() {
            const s = $('#start').val();
            const e_ = $('#end').val();

            fetchChartData(s, e_);
            fetchTop5Charts(s, e_);
            reloadAllCharts();

            $('#filterForm').submit(function(e) {
                e.preventDefault();
                const s = $('#start').val();
                const e_ = $('#end').val();
                fetchChartData(s, e_);
                fetchTop5Charts(s, e_);
                reloadAllCharts();
            });

            $('#exportExcel').click(function(e) {
                e.preventDefault();
                const start = $('#start').val();
                const end = $('#end').val();
                window.location.href = `/service/logs/export_login_logs_excel.php?start=${start}&end=${end}`;
            });
        });
    </script>
</body>

</html>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrator Login</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico">

    <!-- Important Stylesheets -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/css/adminlte.min.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Sweet Alert -->
    <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center"><b>Administrator Login</b></div>
            <div class="card-body">
                <form id="adminLoginForm">
                    <div class="input-group mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Username" required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">เข้าสู่ระบบ</button>
                </form>
                <div class="row mt-2">
                    <div class="col-sm-12 text-right">
                        <a href="/lab_revenue/login" class="text-primary">Staff Mode</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Important Scripts -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/adminlte.min.js"></script>

    <!-- Sweet Alert -->
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
    <script>
        $('#adminLoginForm').on('submit', function(e) {
            e.preventDefault();
            $.post('service/auth/check_admin_login.php', $(this).serialize(), function(res) {
                if (res === 'success') {
                    location.href = '/lab_revenue/admin_dashboard';
                } else {
                    Swal.fire('ผิดพลาด', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง', 'error');
                }
            });
        });
    </script>
</body>

</html>
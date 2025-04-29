<?php
session_start();
require '../../service/connect.php';

// เช็กว่า login หรือยัง
if (!isset($_SESSION['staff_id'])) {
    header('Location: /lab_revenue/login');
    exit;
}

$staff_id = $_SESSION['staff_id'];

// ดึงข้อมูล staff
$stmt = $conn->prepare("
    SELECT s.*, d.name AS department_name, du.name AS duty_name, p.name AS permission_name
    FROM staff s
    LEFT JOIN department d ON s.department_id = d.id
    LEFT JOIN duty du ON s.duty_id = du.id
    LEFT JOIN permission p ON s.permission_id = p.id
    WHERE s.id = ?
");
$stmt->bind_param('i', $staff_id);
$stmt->execute();
$res = $stmt->get_result();
$staff = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <base href="/lab_revenue/">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>ข้อมูลส่วนตัว</title>
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
                            <h2>👤 ข้อมูลส่วนตัว</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/lab_revenue/admin_dashboard">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">ข้อมูลส่วนตัว</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <div class="content">
                <form id="profileForm">
                    <input type="hidden" name="id" value="<?= $staff['id'] ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <label>ชื่อจริง</label>
                            <input type="text" name="fname" class="form-control" value="<?= htmlspecialchars($staff['fname']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>นามสกุล</label>
                            <input type="text" name="lname" class="form-control" value="<?= htmlspecialchars($staff['lname']) ?>" required>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label>เบอร์โทรศัพท์</label>
                            <input type="text" name="tel" class="form-control" value="<?= htmlspecialchars($staff['phone']) ?>">
                        </div>
                        <div class="col-md-6 mt-2">
                            <label>อีเมล</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($staff['email']) ?>">
                        </div>
                    </div>
                    <div class="mt-3 text-right">
                        <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                        <button type="button" class="btn btn-warning" id="changePasswordBtn">เปลี่ยนรหัสผ่าน</button>
                    </div>
                </form>

                <!-- Modal เปลี่ยนรหัสผ่าน -->
                <div class="modal fade" id="changePasswordModal" tabindex="-1">
                    <div class="modal-dialog">
                        <form id="changePasswordForm" class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">เปลี่ยนรหัสผ่าน</h5>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>รหัสผ่านใหม่</label>
                                    <input type="password" name="new_password" id="new_password" class="form-control" required>
                                    <small id="passwordHelp" class="form-text text-muted"></small>
                                </div>
                                <div class="form-group">
                                    <label>ยืนยันรหัสผ่านใหม่</label>
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">บันทึกรหัสผ่านใหม่</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                            </div>
                        </form>
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

        $(function() {

            // บันทึกข้อมูลโปรไฟล์
            $('#profileForm').submit(function(e) {
                e.preventDefault();
                $.post('/lab_revenue/service/user/update_profile.php', $(this).serialize(), function() {
                    Swal.fire('บันทึกสำเร็จ', '', 'success');
                }).fail(function() {
                    Swal.fire('ผิดพลาด', '', 'error');
                });
            });

            // เปิด modal เปลี่ยนรหัสผ่าน
            $('#changePasswordBtn').click(function() {
                $('#changePasswordModal').modal('show');
            });

            // ตรวจสอบความแข็งแรงของรหัสผ่าน
            $('#new_password').keyup(function() {
                let password = $(this).val();
                let strength = 'รหัสผ่านไม่ปลอดภัย';
                if (password.length >= 8 &&
                    /[A-Z]/.test(password) &&
                    /[a-z]/.test(password) &&
                    /\d/.test(password) &&
                    /[^A-Za-z0-9]/.test(password)) {
                    strength = 'รหัสผ่านปลอดภัย ✅';
                }
                $('#passwordHelp').text(strength);
            });

            // บันทึกรหัสผ่านใหม่
            $('#changePasswordForm').submit(function(e) {
                e.preventDefault();
                let newPassword = $('#new_password').val();
                let confirmPassword = $('#confirm_password').val();

                if (newPassword !== confirmPassword) {
                    Swal.fire('ผิดพลาด', 'รหัสผ่านไม่ตรงกัน', 'error');
                    return;
                }

                $.post('/lab_revenue/service/user/change_password.php', {
                    new_password: newPassword
                }, function() {
                    Swal.fire('เปลี่ยนรหัสผ่านสำเร็จ', '', 'success');
                    $('#changePasswordModal').modal('hide');
                }).fail(function(xhr) {
                    Swal.fire('ผิดพลาด', xhr.responseText, 'error');
                });
            });

        });
    </script>

</body>

</html>
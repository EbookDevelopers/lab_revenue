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
    <title>Approve เจ้าหน้าที่</title>
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
    <!-- Modal -->
    <!-- Modal แก้ไข/ดูข้อมูล -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="editForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขข้อมูลเจ้าหน้าที่</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-group">
                        <label>ชื่อจริง</label>
                        <input type="text" name="fname" id="edit_fname" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>นามสกุล</label>
                        <input type="text" name="lname" id="edit_lname" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>เบอร์โทรศัพท์</label>
                        <input type="text" name="phone" id="edit_phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>อีเมล</label>
                        <input type="email" name="email" id="edit_email" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                </div>
            </form>
        </div>
    </div>
    <!-- ./Modal แก้ไข/ดูข้อมูล -->

    <div class="wrapper">
        <!-- Sidebar & Navbar -->
        <?php include_once(__DIR__ . '/../includes/sidebar.php'); ?>
        <div class="content-wrapper p-3">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h3>รายการเจ้าหน้าที่ที่รออนุมัติ</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../dashboard/index.php">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">รายการเจ้าหน้าที่ที่รออนุมัติ</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <div class="content">
                <!-- Main content -->
                <table id="staffTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>
                                <center>ชื่อ</center>
                            </th>
                            <th>
                                <center>ตำแหน่ง</center>
                            </th>
                            <th>
                                <center>กลุ่มงาน</center>
                            </th>
                            <th>
                                <center>เบอร์โทร</center>
                            </th>
                            <th>
                                <center>อีเมล์</center>
                            </th>
                            <th>
                                <center>สถานะ</center>
                            </th>
                            <th>
                                <center>ดำเนินการ</center>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated here by DataTables plugin -->
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

    <!-- Form Validation -->
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="plugins/jquery-validation/additional-methods.min.js"></script>

    <!-- JQuery DataTable -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>

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

        /*
        $(function() {
            $('#staffTable').DataTable();

            $('.approveBtn').click(function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'ยืนยันการอนุมัติ?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'อนุมัติ',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('/lab_revenue/service/admin/approve_staff_action.php', {
                            id: id
                        }, function(res) {
                            if (res === 'success') {
                                Swal.fire('เรียบร้อย', 'อนุมัติแล้ว', 'success').then(() => location.reload());
                            } else {
                                Swal.fire('ผิดพลาด', 'ไม่สามารถอนุมัติได้', 'error');
                            }
                        });
                    }
                });
            });
        });*/

        $(function() {
            const table = $('#staffTable').DataTable({
                ajax: {
                    url: '/lab_revenue/service/admin/get_staff_list_full.php',
                    dataSrc: ''
                },
                columns: [{
                        data: 'fullname'
                    },
                    {
                        data: 'duty'
                    },
                    {
                        data: 'department'
                    },
                    {
                        data: 'phone'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'is_approved',
                        render: function(data) {
                            return data == 1 ? '<span class="badge badge-success">อนุมัติแล้ว</span>' : '<span class="badge badge-warning">รออนุมัติ</span>';
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            let approveBtn = row.is_approved == 1 ?
                                '<button class="btn btn-secondary btn-sm" disabled>✔️ อนุมัติแล้ว</button>' :
                                `<button class="btn btn-success btn-sm approveBtn" data-id="${row.id}">✔️ อนุมัติ</button>`;
                            return `
            ${approveBtn}
            <button class="btn btn-info btn-sm editBtn" data-id="${row.id}">✏️ ดู/แก้ไข</button>
            <button class="btn btn-danger btn-sm deleteBtn" data-id="${row.id}">🗑️ ลบ</button>
          `;
                        }
                    }
                ]
            });

            // Approve
            $(document).on('click', '.approveBtn', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'อนุมัติผู้ใช้งาน?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'อนุมัติ',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('/lab_revenue/service/admin/approve_staff_action.php', {
                            id
                        }, function() {
                            Swal.fire('สำเร็จ', 'อนุมัติเรียบร้อย', 'success');
                            table.ajax.reload();
                        });
                    }
                });
            });

            // ดู/แก้ไข
            $(document).on('click', '.editBtn', function() {
                const id = $(this).data('id');
                $.get('/lab_revenue/service/admin/get_staff_detail.php', {
                    id
                }, function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_fname').val(data.fname);
                    $('#edit_lname').val(data.lname);
                    $('#edit_phone').val(data.phone);
                    $('#edit_email').val(data.email);
                    $('#editModal').modal('show');
                }, 'json');
            });

            $('#editForm').submit(function(e) {
                e.preventDefault();
                $.post('/lab_revenue/service/admin/update_staff_detail.php', $(this).serialize(), function() {
                    Swal.fire('สำเร็จ', 'แก้ไขข้อมูลเรียบร้อย', 'success');
                    $('#editModal').modal('hide');
                    table.ajax.reload();
                });
            });

            // ลบ
            $(document).on('click', '.deleteBtn', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'ต้องการลบข้อมูล?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ลบ',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('/lab_revenue/service/admin/delete_staff.php', {
                            id
                        }, function() {
                            Swal.fire('ลบแล้ว', '', 'success');
                            table.ajax.reload();
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>
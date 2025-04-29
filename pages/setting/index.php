<?php
session_start();
require '../../service/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /lab_revenue/login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <base href="/lab_revenue/">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>ตั้งค่าข้อมูลพื้นฐาน</title>
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
                            <h2>🛠️ ตั้งค่าข้อมูลพื้นฐาน</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/lab_revenue/admin_dashboard">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">ตั้งค่าข้อมูลพื้นฐาน</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <div class="content">
                <h2>🛠️ จัดการประเภทสิทธิการรักษา</h2>
                <!-- Form เพิ่มสิทธิ -->
                <form id="situationForm" class="mb-4">
                    <div class="row">
                        <div class="col-md-8">
                            <input type="text" name="situation_name" id="situation_name" class="form-control" placeholder="ชื่อประเภทสิทธิ" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-block">เพิ่มประเภทสิทธิ</button>
                        </div>
                    </div>
                </form>

                <!-- ตารางข้อมูล -->
                <table id="situationTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>รหัส</th>
                            <th>ชื่อประเภทสิทธิ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                </table>

                <!-- Modal แก้ไข -->
                <div class="modal fade" id="editModal" tabindex="-1">
                    <div class="modal-dialog">
                        <form id="editForm" class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">แก้ไขประเภทสิทธิ</h5>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" id="edit_id">
                                <div class="form-group">
                                    <label>ชื่อประเภทสิทธิ</label>
                                    <input type="text" name="edit_name" id="edit_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">บันทึก</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                            </div>
                        </form>
                    </div>
                </div>

                <hr>

                <h2>🧪 จัดการข้อมูล Specimen</h2>

                <div class="mb-3 text-right">
                    <button class="btn btn-primary" id="addBtn">➕ เพิ่ม Specimen</button>
                </div>

                <table id="specimenTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>ชื่อ Specimen</th>
                            <th>ประเภท Site</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                </table>

                <hr>

                <h2>🚚 จัดการประเภทขนส่ง</h2>

                <form id="transportForm" class="mb-4">
                    <div class="row">
                        <div class="col-md-8">
                            <input type="text" name="name" id="name" class="form-control" placeholder="ชื่อประเภทขนส่ง" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-block">➕ เพิ่มประเภทขนส่ง</button>
                        </div>
                    </div>
                </form>

                <table id="transportTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>รหัส</th>
                            <th>ชื่อประเภทขนส่ง</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                </table>

                <!-- Modal แก้ไข -->
                <div class="modal fade" id="editTransportModal" tabindex="-1">
                    <div class="modal-dialog">
                        <form id="editTransportForm" class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">แก้ไขประเภทขนส่ง</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" id="edit_id">
                                <div class="form-group">
                                    <label>ชื่อประเภทขนส่ง</label>
                                    <input type="text" name="edit_name" id="edit_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">บันทึก</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal เพิ่ม/แก้ไข Specimen -->
            <div class="modal fade" id="specimenModal" tabindex="-1">
                <div class="modal-dialog">
                    <form id="specimenForm" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="specimenModalTitle"></h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="specimenId">

                            <div class="form-group">
                                <label>ชื่อ Specimen</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>ประเภท Site</label>
                                <select name="site" id="site" class="form-control" required>
                                    <option value="">-- เลือก --</option>
                                    <option value="Intra pulmonary">Intra pulmonary</option>
                                    <option value="Extra pulmonary">Extra pulmonary</option>
                                </select>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">บันทึก</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                        </div>
                    </form>
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

        let specimenTable;
        let situationTable;
        let transportTable;

        $(function() {
            transportTable = $('#transportTable').DataTable({
                ajax: {
                    url: 'service/setting/get_transport_list.php',
                    dataSrc: ''
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'id',
                        render: function(id) {
                            return `
            <button class="btn btn-info btn-sm editBtn" data-id="${id}">✏️ แก้ไข</button>
            <button class="btn btn-danger btn-sm deleteBtn" data-id="${id}">🗑️ ลบ</button>
          `;
                        }
                    }
                ]
            });

            // เพิ่มข้อมูล
            $('#transportForm').submit(function(e) {
                e.preventDefault();
                $.post('service/setting/save_transport.php', $(this).serialize(), function() {
                    Swal.fire('เพิ่มข้อมูลสำเร็จ', '', 'success');
                    $('#transportForm')[0].reset();
                    table.ajax.reload();
                }).fail(function() {
                    Swal.fire('ผิดพลาด', '', 'error');
                });
            });

            // แก้ไขข้อมูล
            $(document).on('click', '.editBtn', function() {
                const id = $(this).data('id');
                $.get('service/setting/get_transport_detail.php', {
                    id
                }, function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#editTransportModal').modal('show');
                }, 'json');
            });

            $('#editTransportForm').submit(function(e) {
                e.preventDefault();
                $.post('service/setting/update_transport.php', $(this).serialize(), function() {
                    Swal.fire('แก้ไขข้อมูลสำเร็จ', '', 'success');
                    $('#editTransportModal').modal('hide');
                    table.ajax.reload();
                });
            });

            // ลบข้อมูล
            $(document).on('click', '.deleteBtn', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ลบ',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('service/setting/delete_transport.php', {
                            id
                        }, function() {
                            Swal.fire('ลบข้อมูลสำเร็จ', '', 'success');
                            table.ajax.reload();
                        });
                    }
                });
            });

            // โหลดตาราง Specimen
            specimenTable = $('#specimenTable').DataTable({
                ajax: {
                    url: '/lab_revenue/service/setting/get_specimen_list.php',
                    dataSrc: ''
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'site'
                    },
                    {
                        data: 'id',
                        render: function(id) {
                            return `
            <button class="btn btn-warning btn-sm editBtnSpecimen" data-id="${id}">แก้ไข</button>
            <button class="btn btn-danger btn-sm deleteBtnSpecimen" data-id="${id}">ลบ</button>
          `;
                        }
                    }
                ]
            });

            // โหลดตาราง Situation
            situationTable = $('#situationTable').DataTable({
                ajax: {
                    url: '/lab_revenue/service/setting/get_situation_list.php',
                    dataSrc: ''
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'id',
                        render: function(id) {
                            return `
            <button class="btn btn-info btn-sm editBtnSituation" data-id="${id}">✏️ แก้ไข</button>
            <button class="btn btn-danger btn-sm deleteBtnSituation" data-id="${id}">🗑️ ลบ</button>
          `;
                        }
                    }
                ]
            });

            // ================= Specimen Events =================

            // เพิ่ม Specimen
            $('#addBtn').click(function() {
                $('#specimenModalTitle').text('เพิ่ม Specimen');
                $('#specimenForm')[0].reset();
                $('#specimenId').val('');
                $('#specimenModal').modal('show');
            });

            // แก้ไข Specimen
            $('#specimenTable').on('click', '.editBtnSpecimen', function() {
                let id = $(this).data('id');
                $.get('/lab_revenue/service/setting/get_specimen_detail.php', {
                    id
                }, function(data) {
                    $('#specimenModalTitle').text('แก้ไข Specimen');
                    $('#specimenId').val(data.id);
                    $('#name').val(data.name);
                    $('#site').val(data.site);
                    $('#specimenModal').modal('show');
                }, 'json');
            });

            // ลบ Specimen
            $('#specimenTable').on('click', '.deleteBtnSpecimen', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'ยืนยันการลบ Specimen?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('/lab_revenue/service/setting/delete_specimen.php', {
                            id
                        }, function() {
                            Swal.fire('ลบข้อมูล Specimen สำเร็จ', '', 'success');
                            specimenTable.ajax.reload();
                        });
                    }
                });
            });

            // บันทึก Specimen
            $('#specimenForm').submit(function(e) {
                e.preventDefault();
                let url = $('#specimenId').val() ? '/lab_revenue/service/setting/update_specimen.php' : '/lab_revenue/service/setting/insert_specimen.php';
                $.post(url, $(this).serialize(), function() {
                    Swal.fire('บันทึก Specimen สำเร็จ', '', 'success');
                    $('#specimenModal').modal('hide');
                    specimenTable.ajax.reload();
                }).fail(function() {
                    Swal.fire('ผิดพลาด', '', 'error');
                });
            });

            // ================= Situation Events =================

            // เพิ่ม Situation
            $('#situationForm').submit(function(e) {
                e.preventDefault();
                $.post('/lab_revenue/service/setting/save_situation.php', $(this).serialize(), function() {
                    Swal.fire('เพิ่มประเภทสิทธิสำเร็จ', '', 'success');
                    $('#situationForm')[0].reset();
                    situationTable.ajax.reload();
                }).fail(function() {
                    Swal.fire('ผิดพลาด', '', 'error');
                });
            });

            // แก้ไข Situation
            $(document).on('click', '.editBtnSituation', function() {
                let id = $(this).data('id');
                $.get('/lab_revenue/service/setting/get_situation_detail.php', {
                    id
                }, function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#editModal').modal('show');
                }, 'json');
            });

            $('#editForm').submit(function(e) {
                e.preventDefault();
                $.post('/lab_revenue/service/setting/update_situation.php', $(this).serialize(), function() {
                    Swal.fire('แก้ไขประเภทสิทธิสำเร็จ', '', 'success');
                    $('#editModal').modal('hide');
                    situationTable.ajax.reload();
                });
            });

            // ลบ Situation
            $(document).on('click', '.deleteBtnSituation', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'ยืนยันการลบประเภทสิทธิ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ลบ',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('/lab_revenue/service/setting/delete_situation.php', {
                            id
                        }, function() {
                            Swal.fire('ลบข้อมูลสำเร็จ', '', 'success');
                            situationTable.ajax.reload();
                        });
                    }
                });
            });

        });
    </script>

</html>
<?php
require '../../service/connect.php';
require '../../service/auth/check_permission.php';
if (!isset($_SESSION['staff_id'])) {
    header("Location: /lab_revenue/login");
    exit;
}
checkPermission([1, 3, 5]);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <base href="/lab_revenue/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการหนังสือส่งตรวจ</title>
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
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="modal fade" id="editSendBookModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <form id="editSendBookForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">✏️ แก้ไขหนังสือนำส่ง</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="row">
                        <div class="col-md-6">
                            <label>เลขที่หนังสือ</label>
                            <input type="text" name="book_number" id="edit_book_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>วันที่หนังสือ</label>
                            <input type="date" name="book_date" id="edit_book_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label>จำนวนตัวอย่าง</label>
                            <input type="number" name="sample_quantity" id="edit_sample_quantity" class="form-control" required>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label>อุณหภูมิ</label>
                            <input type="text" name="temperature" id="edit_temperature" class="form-control">
                        </div>
                        <!-- เพิ่ม field อื่น ๆ ตามโครงสร้างเดิมของคุณ -->
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">💾 บันทึก</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">❌ ยกเลิก</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal เพิ่มหนังสือส่งตรวจ -->
    <div class="modal fade" id="addSendBookModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="addSendBookForm">
                    <div class="modal-header">
                        <h5 class="modal-title">➕ เพิ่มหนังสือส่งตรวจ</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label>โรงพยาบาล</label>
                                <select name="hospital_id" id="hospital_id" class="form-control select2" required></select>
                            </div>

                            <div class="col-md-6">
                                <label>เลขที่หนังสือ</label>
                                <input type="text" name="book_number" class="form-control" required>
                            </div>

                            <div class="col-md-6 mt-3">
                                <label>วันที่ออกหนังสือ</label>
                                <input type="date" name="book_date" class="form-control" required>
                            </div>

                            <div class="col-md-6 mt-3">
                                <label>วันที่ส่งตรวจ</label>
                                <input type="date" name="send_date" class="form-control" required>
                            </div>

                            <div class="col-md-6 mt-3">
                                <label>วัน/เวลารับตัวอย่าง</label>
                                <input type="datetime-local" name="received_datetime" class="form-control" required>
                            </div>

                            <div class="col-md-6 mt-3">
                                <label>ประเภทสิ่งส่งตรวจ</label>
                                <select name="specimen_id" id="specimen_id" class="form-control select2bs4" required></select>
                            </div>

                            <div class="col-md-4 mt-3">
                                <label>จำนวนตัวอย่าง</label>
                                <input type="number" name="sample_quantity" class="form-control" required>
                            </div>

                            <div class="col-md-4 mt-3">
                                <label>อุณหภูมิสิ่งส่งตรวจ</label>
                                <input type="text" name="sample_temperature" class="form-control">
                            </div>

                            <div class="col-md-4 mt-3">
                                <label>ขนส่งโดย</label>
                                <select name="transporter_id" id="transporter_id" class="form-control select2bs4" required>
                                    <!-- โหลดด้วย AJAX ด้านล่าง -->
                                </select>
                            </div>


                            <div class="col-md-12 mt-3">
                                <label>สภาพสิ่งส่งตรวจ</label>
                                <select name="sample_condition" id="sample_condition" class="form-control" required>
                                    <option value="">-- เลือกสภาพสิ่งส่งตรวจ --</option>
                                    <option value="Normal">ปกติ</option>
                                    <option value="Damaged">มีร่องรอยความเสียหาย</option>
                                </select>
                            </div>

                            <div class="col-md-12 mt-3">
                                <label>ไฟล์รูปภาพหนังสือ</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="book_image" name="book_image" accept="image/*" required>
                                    <label class="custom-file-label" for="book_image">เลือกไฟล์</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="wrapper">
        <?php include_once(__DIR__ . '/../includes/sidebar.php'); ?>
        <div class="content-wrapper p-3">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h2>📋 รายการหนังสือส่งตรวจ</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/lab_revenue/dashboard">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">รายการหนังสือส่งตรวจ</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <div class="content">
                <div class="mb-3">
                    <button class="btn btn-success" id="openAddModal">➕ เพิ่มหนังสือส่งตรวจ</button>
                </div>
                <!-- <div class="mb-3">
                    <a href="/lab_revenue/add_send_book" class="btn btn-success">➕ เพิ่มหนังสือส่งตรวจ</a>
                     <a href="/lab_revenue/service/send_book/export_send_book_excel.php" target="_blank" class="btn btn-success">📥 Export Excel</a>
                    <a href="/lab_revenue/service/send_book/export_send_book_pdf.php" target="_blank" class="btn btn-danger">📄 Export PDF</a>
                </div> -->

                <table id="sendBookTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>โรงพยาบาล</th>
                            <th>เลขที่หนังสือ</th>
                            <th>วันที่ออกหนังสือ</th>
                            <th>ประเภทสิ่งส่งตรวจ</th>
                            <th>จำนวนตัวอย่าง</th>
                            <th>สถานะ</th>
                            <th>รูปหนังสือ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                </table>

                <!-- Modal ดูรูป -->
                <div class="modal fade" id="imageModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <img id="previewImage" src="" class="img-fluid" alt="รูปหนังสือ">
                            </div>
                        </div>
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

    <!-- JQuery DataTable -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>

    <!-- Sweet Alert -->
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>

    <!-- jszip and pdfmake for DataTable export -->
    <script src="/lab_revenue/plugins/jszip/jszip.min.js"></script>
    <script src="/lab_revenue/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="/lab_revenue/plugins/pdfmake/vfs_fonts.js"></script>

    <!-- Select2 -->
    <script src="plugins/select2/js/select2.full.min.js"></script>

    <!-- BS Custom File Input -->
    <script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>

    <script>
        bsCustomFileInput.init();

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
            $('#sendBookTable').on('click', '.editBtn', function() {
                const id = $(this).data('id');
                $.get('/lab_revenue/service/send_book/get_send_book_detail.php', {
                    id
                }, function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_book_number').val(data.book_number);
                    $('#edit_book_date').val(data.book_date);
                    $('#edit_sample_quantity').val(data.sample_quantity);
                    $('#edit_temperature').val(data.temperature);
                    // เติมข้อมูลฟิลด์อื่น ๆ ที่คุณมี
                    $('#editSendBookModal').modal('show');
                }, 'json');
            });


            $('#openAddModal').click(function() {
                $('#addSendBookForm')[0].reset();
                $('#hospital_id').val(null).trigger('change');
                $('#specimen_id').val(null).trigger('change');
                $('#addSendBookModal').modal('show');

                // โหลด select2bs4 โรงพยาบาล
                $('#hospital_id').select2({
                    theme: 'bootstrap4',
                    placeholder: '-- เลือกโรงพยาบาล --',
                    minimumInputLength: 2,
                    ajax: {
                        url: '/lab_revenue/service/ajax_search_hospital.php',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });

                // โหลด select2bs4 Specimen
                $('#specimen_id').select2({
                    theme: 'bootstrap4',
                    placeholder: '-- เลือก Specimen --',
                    minimumInputLength: 1,
                    ajax: {
                        url: '/lab_revenue/service/ajax_search_specimen.php',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term || ''
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });

                // ตอนเปิด modal ล้างข้อมูลเก่า
                $('#transporter_id').html('<option value="">-- เลือกประเภทการขนส่ง --</option>');

                // ดึงรายการ transport_type ทั้งหมด
                $.ajax({
                    url: 'service/send_book/get_transport_type_list.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        data.forEach(function(item) {
                            $('#transporter_id').append(`<option value="${item.id}">${item.name}</option>`);
                        });
                    },
                    error: function() {
                        console.error('โหลดข้อมูลประเภทขนส่งไม่สำเร็จ');
                    }
                });
            });

            $('#editSendBookForm').submit(function(e) {
                e.preventDefault();
                $.post('/lab_revenue/service/send_book/update_send_book.php', $(this).serialize(), function(response) {
                    Swal.fire('สำเร็จ', 'บันทึกการแก้ไขเรียบร้อย', 'success');
                    $('#editSendBookModal').modal('hide');
                    $('#sendBookTable').DataTable().ajax.reload();
                }).fail(function(xhr) {
                    Swal.fire('ผิดพลาด', xhr.responseText, 'error');
                });
            });



            // บันทึกฟอร์ม
            $('#addSendBookForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: '/lab_revenue/service/send_book/save_send_book.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        Swal.fire('บันทึกสำเร็จ', '', 'success');
                        $('#addSendBookModal').modal('hide');
                        $('#sendBookTable').DataTable().ajax.reload(); // รีโหลดตาราง
                    },
                    error: function() {
                        Swal.fire('ผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                    }
                });

            });

            const table = $('#sendBookTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        text: 'Export Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'Export CSV',
                        className: 'btn btn-info btn-sm'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'Export PDF',
                        className: 'btn btn-danger btn-sm'
                    }
                ],
                ajax: {
                    url: '/lab_revenue/service/send_book/get_send_book_list.php',
                    dataSrc: ''
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'hospital_name'
                    },
                    {
                        data: 'book_number'
                    },
                    {
                        data: 'book_date'
                    },
                    {
                        data: 'specimen_name'
                    },
                    {
                        data: 'sample_quantity'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'book_image',
                        render: function(file) {
                            return `<button class="btn btn-info btn-sm viewImageBtn" data-file="${file}">ดูรูป</button>`;
                        }
                    },
                    {
                        data: 'id',
                        render: function(id, type, row) {
                            if (row.status === 'Received') {
                                return `
      <button class="btn btn-success btn-sm approveBtn" data-id="${id}">✅ อนุมัติ</button>
      <button class="btn btn-primary btn-sm editBtn" data-id="${id}">✏️ แก้ไข</button>
      <button class="btn btn-danger btn-sm deleteBtn" data-id="${id}">ลบ</button>
    `;
                            } else {
                                return `
      <button class="btn btn-primary btn-sm printBtn" data-id="${id}">🖨️ พิมพ์</button>
      <button class="btn btn-primary btn-sm editBtn" data-id="${id}">✏️ แก้ไข</button>
      <button class="btn btn-danger btn-sm deleteBtn" data-id="${id}">ลบ</button>
    `;
                            }
                        }

                    }
                ]
            });

            $('#sendBookTable').on('click', '.approveBtn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'ยืนยันการอนุมัติ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่, อนุมัติเลย!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('/lab_revenue/service/send_book/approve_send_book.php', {
                            id: id
                        }, function() {
                            Swal.fire('อนุมัติเรียบร้อย', '', 'success');
                            table.ajax.reload();
                        });
                    }
                });
            });


            // กดปุ่มพิมพ์หนังสือ
            $('#sendBookTable').on('click', '.printBtn', function() {
                let id = $(this).data('id');
                window.open('/lab_revenue/service/send_book/print_send_book.php?id=' + id, '_blank');
            });

            // เปิดรูปภาพ
            $('#sendBookTable').on('click', '.viewImageBtn', function() {
                let file = $(this).data('file');
                $('#previewImage').attr('src', '/lab_revenue/uploads/send_books/' + file);
                $('#imageModal').modal('show');
            });

            // (option) ลบข้อมูล
            $('#sendBookTable').on('click', '.deleteBtn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('/lab_revenue/service/send_book/delete_send_book.php', {
                            id: id
                        }, function() {
                            Swal.fire('ลบสำเร็จ', '', 'success');
                            table.ajax.reload();
                        }).fail(function() {
                            Swal.fire('ผิดพลาด', '', 'error');
                        });
                    }
                });
            });

        });
    </script>

</body>

</html>
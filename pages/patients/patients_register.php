<?php
//session_start();
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
    <title>ข้อมูลผู้เข้ารับบริการ</title>
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

    <!-- Select2 -->
    <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">


</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include_once(__DIR__ . '/../includes/sidebar.php'); ?>
        <div class="content-wrapper p-3">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h2>🧾 ข้อมูลผู้เข้ารับบริการ</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/lab_revenue/admin_dashboard">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">ข้อมูลผู้เข้ารับบริการ</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <div class="content">
                <h2>📥 นำเข้าข้อมูลผู้ป่วยจากไฟล์</h2>

                <form id="importForm" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="patient_file" name="patient_file" accept=".xlsx, .xls, .csv" required>
                                <label class="custom-file-label" for="patient_file">เลือกไฟล์...</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success btn-block">นำเข้าข้อมูล</button>
                        </div>
                    </div>
                </form>
                <div class="progress mt-2" style="height: 25px; display: none;">
                    <div id="uploadProgress" class="progress-bar progress-bar-striped progress-bar-animated"
                        role="progressbar" style="width: 0%">0%</div>
                </div>


                <hr>

                <h2>🧾 ลงทะเบียนผู้ป่วย</h2>
                <!-- Form เพิ่มผู้ป่วย -->
                <form id="patientForm" class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <label>NAP Number หรือ ชื่อ-นามสกุล</label>
                            <input type="text" name="nap_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>โรงพยาบาล</label>
                            <select name="hospital_id" id="hospital_id" class="form-control select2bs4" required style="width: 100%;">
                                <option value="">-- พิมพ์ชื่อหรือรหัสโรงพยาบาลเพื่อค้นหา --</option>
                            </select>

                        </div>
                        <div class="col-md-6 mt-2">
                            <label>HN</label>
                            <input type="text" name="hn" class="form-control" required>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label>อายุ</label>
                            <input type="number" name="age" class="form-control" required min="0">
                        </div>
                        <div class="col-md-3 mt-2">
                            <label>เพศ</label>
                            <select name="sex" class="form-control" required>
                                <option value="">-- เลือกเพศ --</option>
                                <option value="ชาย">ชาย</option>
                                <option value="หญิง">หญิง</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-right mt-3">
                        <button type="submit" class="btn btn-primary">บันทึกข้อมูลผู้ป่วย</button>
                    </div>
                </form>

                <hr>
                <h2>📋 Export รายชื่อผู้ป่วย (เลือกช่วงวันที่ + โรงพยาบาล)</h2>

                <form id="exportForm" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <label>วันที่เริ่มต้น</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>วันที่สิ้นสุด</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>เลือกโรงพยาบาล (เลือกได้หลายที่)</label>
                            <select name="hospital_ids[]" id="hospital_ids" class="form-control select2bs4" multiple="multiple" required>
                                <!-- โหลดตัวเลือกโรงพยาบาลด้วย jQuery ด้านล่าง -->
                            </select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="d-flex gap-2">
                                <button type="button" id="exportCSV" class="btn btn-primary mr-2">
                                    <i class="fas fa-file-csv"></i> Export CSV
                                </button>
                                <button type="button" id="exportExcel" class="btn btn-success mr-2">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </button>
                                <button type="button" id="exportPDF" class="btn btn-danger">
                                    <i class="fas fa-file-pdf"></i> Export PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <hr>

                <h2>📋 รายชื่อผู้ป่วย</h2>

                <table id="patientTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>NAP Number</th>
                            <th>HN</th>
                            <th>อายุ</th>
                            <th>เพศ</th>
                            <th>โรงพยาบาล</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                </table>


            </div>
        </div>
        <?php include_once(__DIR__ . '/../includes/footer.php'); ?>
    </div>

    <!-- Modal ดู/แก้ไขข้อมูล -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="editPatientForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขข้อมูลผู้ป่วย</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-group">
                        <label>NAP Number</label>
                        <input type="text" name="nap_number" id="edit_nap_number" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>HN</label>
                        <input type="text" name="hn" id="edit_hn" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>อายุ</label>
                        <input type="number" name="age" id="edit_age" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>เพศ</label>
                        <select name="sex" id="edit_sex" class="form-control" required>
                            <option value="ชาย">ชาย</option>
                            <option value="หญิง">หญิง</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                </div>
            </form>
        </div>
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

    <!-- Select2 -->
    <script src="plugins/select2/js/select2.full.min.js"></script>

    <!-- BS Custom File Input -->
    <script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
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
            $('#importForm').submit(function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                // รีเซ็ต progress bar
                $('.progress').show();
                $('#uploadProgress').css('width', '0%').text('0%');

                $.ajax({
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();

                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                                $('#uploadProgress').css('width', percentComplete + '%').text(percentComplete + '%');
                            }
                        }, false);

                        return xhr;
                    },
                    url: '/lab_revenue/service/patients/import_patient.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire('สำเร็จ', response, 'success');
                        $('.progress').hide(); // ซ่อน progress bar เมื่อเสร็จ
                        $('#patientTable').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire('ผิดพลาด', xhr.responseText, 'error');
                        $('.progress').hide(); // ซ่อน progress bar แม้ error
                    }
                });
            });

            bsCustomFileInput.init();

            // Init select2
            $('#hospital_ids').select2({
                theme: 'bootstrap4',
                placeholder: "-- พิมพ์ชื่อหรือรหัสโรงพยาบาล --",
                ajax: {
                    url: '/lab_revenue/service/patients/ajax_search_hospital.php',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data // <-- ตรงนี้สำคัญ
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1,
                width: 'resolve'
            });



            // Load hospital list
            /*$.get('/lab_revenue/service/patients/get_select_data.php', {
                type: 'hospital'
            }, function(data) {
                data.forEach(function(item) {
                    $('#hospital_ids').append(`<option value="${item.id}">[${item.code}] ${item.name}</option>`);
                });
            });*/

            function validateForm() {
                const start = $('input[name="start_date"]').val();
                const end = $('input[name="end_date"]').val();
                const hospital = $('#hospital_ids').val();
                if (!start || !end || !hospital.length) {
                    Swal.fire('ผิดพลาด', 'กรุณาเลือกวันที่และโรงพยาบาล', 'error');
                    return false;
                }
                if (start > end) {
                    Swal.fire('ผิดพลาด', 'วันที่เริ่มต้นต้องน้อยกว่าหรือเท่ากับวันที่สิ้นสุด', 'error');
                    return false;
                }
                return true;
            }

            function buildURL(base) {
                const start = $('input[name="start_date"]').val();
                const end = $('input[name="end_date"]').val();
                const hospitals = $('#hospital_ids').val().join(',');
                return `${base}?start_date=${start}&end_date=${end}&hospital_ids=${hospitals}`;
            }

            $('#exportCSV').click(function() {
                if (validateForm()) {
                    window.open(buildURL('/lab_revenue/service/patients/export_patient_csv.php'), '_blank');
                }
            });

            $('#exportExcel').click(function() {
                if (validateForm()) {
                    window.open(buildURL('/lab_revenue/service/patients/export_patient_excel.php'), '_blank');
                }
            });

            $('#exportPDF').click(function() {
                if (validateForm()) {
                    window.open(buildURL('/lab_revenue/service/patients/export_patient_pdf.php'), '_blank');
                }
            });

        });

        // เริ่มต้น select2
        $('#hospital_id').select2({
            theme: 'bootstrap4',
            placeholder: "-- พิมพ์ชื่อหรือรหัสโรงพยาบาล --",
            ajax: {
                url: '/lab_revenue/service/patients/ajax_search_hospital.php',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
            width: 'resolve'
        });


        // โหลดข้อมูลโรงพยาบาล
        $.ajax({
            url: '/lab_revenue/service/patients/get_select_data.php',
            data: {
                type: 'hospital'
            },
            method: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#hospital_id').html('<option>กำลังโหลดข้อมูลโรงพยาบาล...</option>');
            },
            success: function(data) {
                $('#hospital_id').empty().append('<option value="">-- เลือกโรงพยาบาล --</option>');
                $.each(data, function(i, item) {
                    $('#hospital_id').append(`<option value="${item.id}">[${item.code}] ${item.name}</option>`);
                });
            },
            error: function() {
                Swal.fire('ผิดพลาด', 'โหลดรายชื่อโรงพยาบาลไม่สำเร็จ', 'error');
            }
        });

        // ตารางผู้ป่วย
        const table = $('#patientTable').DataTable({
            ajax: {
                url: '/lab_revenue/service/patients/get_patient_list.php',
                dataSrc: ''
            },
            columns: [{
                    data: 'nap_number'
                },
                {
                    data: 'hn'
                },
                {
                    data: 'age'
                },
                {
                    data: 'sex'
                },
                {
                    data: 'hospital'
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

        // เพิ่มผู้ป่วย
        $('#patientForm').submit(function(e) {
            e.preventDefault();
            $.post('/lab_revenue/service/patients/save_patient.php', $(this).serialize(), function() {
                Swal.fire('บันทึกแล้ว', '', 'success');
                $('#patientForm')[0].reset();
                table.ajax.reload();
            }).fail(function() {
                Swal.fire('ผิดพลาด', 'HN ซ้ำหรือข้อมูลไม่ถูกต้อง', 'error');
            });
        });

        // แก้ไขข้อมูล
        $(document).on('click', '.editBtn', function() {
            const id = $(this).data('id');
            $.get('/lab_revenue/service/patients/get_patient_detail.php', {
                id
            }, function(data) {
                $('#edit_id').val(data.id);
                $('#edit_nap_number').val(data.nap_number);
                $('#edit_hn').val(data.hn);
                $('#edit_age').val(data.age);
                $('#edit_sex').val(data.sex);
                $('#editModal').modal('show');
            }, 'json');
        });

        $('#editPatientForm').submit(function(e) {
            e.preventDefault();
            $.post('/lab_revenue/service/patients/update_patient.php', $(this).serialize(), function() {
                Swal.fire('แก้ไขสำเร็จ', '', 'success');
                $('#editModal').modal('hide');
                table.ajax.reload();
            });
        });

        // ลบข้อมูล
        $(document).on('click', '.deleteBtn', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'ต้องการลบข้อมูลหรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('/lab_revenue/service/patients/delete_patient.php', {
                        id
                    }, function() {
                        Swal.fire('ลบแล้ว', '', 'success');
                        table.ajax.reload();
                    });
                }
            });
        });
    </script>
</body>

</html>
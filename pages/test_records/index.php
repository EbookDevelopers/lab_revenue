<?php
require '../../service/connect.php';
require '../../service/auth/check_permission.php';
if (!isset($_SESSION['staff_id'])) {
    header('Location: /lab_revenue/login');
    exit;
}

checkPermission([1, 5]);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <base href="/lab_revenue/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>รายการผลตรวจ LAB</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico">

    <!-- Important Stylesheets -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/css/adminlte.min.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- jQuery DataTable -->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

    <!-- Sweet Alert -->
    <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <!-- Modal: เพิ่มข้อมูลการตรวจ + ดูประวัติย้อนหลัง -->
    <div class="modal fade" id="addLabTestModal" aria-labelledby="addLabTestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form id="addLabTestForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLabTestModalLabel">➕ เพิ่มข้อมูลการตรวจ Lab</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-4">
                            <label>เลือกผู้ป่วย (NAP Number)</label>
                            <select name="patient_id" id="modal_patient_id" class="form-control select2bs4" style="width:100%;" required></select>
                        </div>

                        <div class="col-md-8" id="modal_patientInfo" style="display: none;">
                            <div class="border p-3 rounded bg-light">
                                <p><strong>NAP Number:</strong> <span id="modal_infoNap"></span></p>
                                <p><strong>HN:</strong> <span id="modal_infoHN"></span></p>
                                <p><strong>อายุ:</strong> <span id="modal_infoAge"></span></p>
                                <p><strong>เพศ:</strong> <span id="modal_infoSex"></span></p>

                                <button type="button" class="btn btn-info btn-sm mt-2" id="modal_viewHistoryBtn">
                                    📋 ดูประวัติการตรวจย้อนหลัง
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label>เลือกหนังสือนำส่ง</label>
                            <select name="send_book_id" id="modal_send_book_id" class="form-control" required>
                                <!-- โหลดด้วย AJAX -->
                            </select>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label>CD4 Number</label>
                            <input type="text" name="cd4_number" class="form-control" required>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label>HN</label>
                            <input type="text" name="hn" class="form-control" required>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label>วันที่ทำการตรวจ (Analysis Date)</label>
                            <input type="date" name="analysis_date" class="form-control" required>
                        </div>

                        <div class="col-md-3 mt-3">
                            <label>WBC</label>
                            <input type="number" name="wbc" class="form-control" required>
                        </div>

                        <div class="col-md-3 mt-3">
                            <label>Lymphocyte</label>
                            <input type="number" step="0.01" name="lymp" class="form-control" required>
                        </div>

                        <div class="col-md-3 mt-3">
                            <label>CD4</label>
                            <input type="number" step="0.01" name="cd4" class="form-control" required>
                        </div>

                        <div class="col-md-3 mt-3">
                            <label>Abs CD4</label>
                            <input type="number" name="abs_cd4" class="form-control" required>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label>สิทธิการตรวจ (Situation)</label>
                            <select name="situation_id" id="modal_situation_id" class="form-control" required>
                                <!-- โหลดข้อมูลจาก Ajax -->
                            </select>
                        </div>

                    </div>

                    <!-- ส่วนแสดงประวัติการตรวจย้อนหลัง -->
                    <div class="mt-4" id="historySection" style="display:none;">
                        <h5>📋 ประวัติการตรวจย้อนหลัง</h5>
                        <table id="historyTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Lab Number</th>
                                    <th>วันที่รับตัวอย่าง</th>
                                    <th>วันที่ตรวจ</th>
                                    <th>WBC</th>
                                    <th>Lymphocyte</th>
                                    <th>CD4</th>
                                    <th>Abs CD4</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                </div>
            </form>
        </div>
    </div>

    <div class="wrapper">
        <?php include_once(__DIR__ . '/../includes/sidebar.php'); ?>
        <div class="content-wrapper p-3">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h2>📋 รายการผลตรวจ LAB</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/lab_revenue/admin_dashboard">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">รายการผลตรวจ LAB</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <div class="content">
                <div class="mb-3 text-right">
                    <button class="btn btn-success" id="openAddLabTestModal">
                        ➕ เพิ่มข้อมูลการตรวจ
                    </button>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>เลือกช่วงเดือน</label>
                        <input type="month" id="startMonth" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>ถึงเดือน</label>
                        <input type="month" id="endMonth" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>เลือกโรงพยาบาล</label>
                        <select id="hospitalFilter" class="form-control select2bs4">
                            <option value="">-- ทั้งหมด --</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary btn-block" id="filterBtn">ค้นหา</button>
                    </div>
                </div>
                <table id="labTable" class="table table-bordered table-striped text-nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>Lab Number</th>
                            <th>NAP Number</th>
                            <th>CD4 Number</th>
                            <th>HN</th>
                            <th>วันที่รับตัวอย่าง</th>
                            <th>วันที่ตรวจ</th>
                            <th>CD4</th>
                            <th>AbsCD4</th>
                            <th>WBC</th>
                            <th>สิทธิ</th>
                        </tr>
                    </thead>
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

    <!-- Select2 -->
    <script src="plugins/select2/js/select2.full.min.js"></script>

    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>

    <script>
        let table; // ✅ ประกาศตัวแปร table ก่อน

        $(document).ready(function() {
            let currentPatientId = null;

            $('#openAddLabTestModal').click(function() {
                $('#addLabTestForm')[0].reset();
                $('#modal_patientInfo').hide();
                $('#historySection').hide();
                $('#addLabTestModal').modal('show');
            });

            // ใช้ Select2 แบบ AJAX
            $('#modal_send_book_id').select2({
                theme: 'bootstrap4',
                placeholder: "-- พิมพ์เลขที่หนังสือ --",
                ajax: {
                    url: '/lab_revenue/service/test_records/ajax_search_send_book.php',
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


            // โหลด Select2 Patient
            $('#modal_patient_id').select2({
                theme: 'bootstrap4',
                placeholder: "-- พิมพ์ NAP Number หรือ HN --",
                ajax: {
                    url: '/lab_revenue/service/test_records/ajax_search_patient.php',
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
                minimumInputLength: 1
            });

            // เลือก Patient แล้วแสดงข้อมูล
            $('#modal_patient_id').on('select2:select', function(e) {
                const patientId = e.params.data.id;
                currentPatientId = patientId;

                $.get('/lab_revenue/service/test_records/get_patient_info.php', {
                    id: patientId
                }, function(data) {
                    $('#modal_patientInfo').show();
                    $('#modal_infoNap').text(data.nap_number);
                    $('#modal_infoHN').text(data.hn);
                    $('#modal_infoAge').text(data.age);
                    $('#modal_infoSex').text(data.sex);
                }, 'json');
            });

            // โหลด Situation
            $.get('/lab_revenue/service/setting/get_situation_list.php', function(data) {
                data.forEach(function(item) {
                    $('#modal_situation_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            }, 'json');

            // ดูประวัติย้อนหลัง
            $('#modal_viewHistoryBtn').click(function() {
                if (!currentPatientId) {
                    Swal.fire('โปรดเลือกผู้ป่วยก่อน', '', 'warning');
                    return;
                }

                $('#historySection').show();

                // ทำลายตารางเก่าถ้ามี
                if ($.fn.DataTable.isDataTable('#historyTable')) {
                    $('#historyTable').DataTable().clear().destroy();
                }

                // โหลดข้อมูลใหม่
                $('#historyTable').DataTable({
                    ajax: {
                        url: '/lab_revenue/service/test_records/get_lab_history.php',
                        type: 'GET',
                        data: {
                            patient_id: currentPatientId
                        },
                        dataSrc: ''
                    },
                    columns: [{
                            data: 'lab_number'
                        },
                        {
                            data: 'received_date'
                        },
                        {
                            data: 'analysis_date'
                        },
                        {
                            data: 'wbc'
                        },
                        {
                            data: 'lymp'
                        },
                        {
                            data: 'cd4'
                        },
                        {
                            data: 'abs_cd4'
                        }
                    ],
                    responsive: true,
                    destroy: true,
                    ordering: true
                });
            });

            // บันทึกข้อมูล
            $('#addLabTestForm').submit(function(e) {
                e.preventDefault();
                $.post('/lab_revenue/service/test_records/insert_lab_test.php', $(this).serialize(), function(response) {
                    Swal.fire('บันทึกสำเร็จ', '', 'success');
                    $('#addLabTestModal').modal('hide');
                    $('#labTestTable').DataTable().ajax.reload(); // Reload ตาราง main
                }).fail(function(xhr) {
                    Swal.fire('ผิดพลาด', xhr.responseText, 'error');
                });
            });

        });

        $(function() {
            // ✅ คลิกปุ่ม Filter ต้อง reload ผ่าน table.ajax.reload() หลังสร้างเสร็จ
            $('#filterBtn').click(function() {
                if (table) {
                    table.ajax.reload();
                } else {
                    console.error('DataTable not initialized!');
                }
            });

            // โหลดโรงพยาบาลลง select2
            $('#hospitalFilter').select2({
                theme: 'bootstrap4',
                placeholder: "-- เลือกโรงพยาบาล --",
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
                minimumInputLength: 1
            });

            // สร้าง DataTable
            table = $('#labTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/lab_revenue/service/test_records/get_lab_test_list.php',
                    data: function(d) {
                        d.startMonth = $('#startMonth').val();
                        d.endMonth = $('#endMonth').val();
                        d.hospital_id = $('#hospitalFilter').val();
                    }
                },
                columns: [{
                        data: 'lab_number'
                    },
                    {
                        data: 'nap_number'
                    },
                    {
                        data: 'cd4_number'
                    },
                    {
                        data: 'hn'
                    },
                    {
                        data: 'received_date'
                    },
                    {
                        data: 'analysis_date'
                    },
                    {
                        data: 'cd4'
                    },
                    {
                        data: 'abs_cd4'
                    },
                    {
                        data: 'wbc'
                    },
                    {
                        data: 'situation_name'
                    }
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        text: 'Export Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'Export CSV',
                        className: 'btn btn-info'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'Export PDF',
                        className: 'btn btn-danger'
                    }
                ]
            });
        });
    </script>

</body>

</html>
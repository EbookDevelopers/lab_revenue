<?php
require '../../service/connect.php';
require '../../service/auth/check_permission.php';
if (!isset($_SESSION['staff_id'])) {
    header('Location: /lab_revenue/login');
    exit;
}

checkPermission([1, 5]);

// ดึง patients และ situation มาให้เลือก
$patients = $conn->query("SELECT id, nap_number FROM patients WHERE is_deleted = 0 ORDER BY nap_number ASC");
$situations = $conn->query("SELECT id, name FROM situation ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <base href="/lab_revenue/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เพิ่มข้อมูลการตรวจ</title>
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
    <div class="wrapper">
        <?php include_once(__DIR__ . '/../includes/sidebar.php'); ?>
        <div class="content-wrapper p-3">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h2>➕ เพิ่มข้อมูลการตรวจ Lab</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/lab_revenue/admin_dashboard">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">เพิ่มข้อมูลการตรวจ Lab</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <div class="content">
                <form id="labForm">
                    <div class="row">
                        <div class="col-md-4">
                            <label>เลือกผู้ป่วย (NAP Number)</label>
                            <select name="patient_id" id="patient_id" class="form-control select2bs4" style="width:100%;" required>
                            </select>
                        </div>

                        <!-- ช่องที่จะแสดงข้อมูล -->
                        <div class="col-md-8" id="patientInfo" style="display: none;">
                            <div class="border p-3 rounded bg-light">
                                <p><strong>NAP Number:</strong> <span id="infoNap"></span></p>
                                <p><strong>HN:</strong> <span id="infoHN"></span></p>
                                <p><strong>อายุ:</strong> <span id="infoAge"></span></p>
                                <p><strong>เพศ:</strong> <span id="infoSex"></span></p>

                                <button type="button" class="btn btn-info btn-sm mt-2" id="viewHistoryBtn">
                                    📋 ดูประวัติการตรวจย้อนหลัง
                                </button>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label>CD4 Number</label>
                            <input type="text" name="cd4_number" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label>HN</label>
                            <input type="text" name="hn" class="form-control" required>
                        </div>

                        <div class="col-md-4">
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
                            <label>สิทธิการตรวจ (situation)</label>
                            <select name="situation_id" class="form-control" required>
                                <option value="">-- เลือกสิทธิการตรวจ --</option>
                                <?php while ($s = $situations->fetch_assoc()): ?>
                                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 text-right">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                    </div>
                </form>

            </div>
        </div>
        <?php include_once(__DIR__ . '/../includes/footer.php'); ?>
    </div>

    <div class="modal fade" id="historyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ประวัติการตรวจย้อนหลัง</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover" id="historyTable">
                        <thead>
                            <tr>
                                <th>Lab Number</th>
                                <th>วันที่รับตัวอย่าง</th>
                                <th>วันที่ตรวจ</th>
                                <th>CD4</th>
                                <th>Abs CD4</th>
                                <th>WBC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- ตารางแสดงข้อมูลย้อนหลัง -->
                        </tbody>
                    </table>
                </div>
            </div>
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

        let currentPatientId = null; // เก็บ id ที่เลือก

        // กดดูประวัติ
        $('#viewHistoryBtn').click(function() {
            if (!currentPatientId) {
                Swal.fire('โปรดเลือกผู้ป่วยก่อน', '', 'warning');
                return;
            }

            // เคลียร์ตาราง
            $('#historyTable tbody').empty();

            $.get('/lab_revenue/service/test_records/get_lab_history.php', {
                patient_id: currentPatientId
            }, function(data) {
                if (data.length > 0) {
                    data.forEach(function(item) {
                        $('#historyTable tbody').append(`
          <tr>
            <td>${item.lab_number}</td>
            <td>${item.received_date}</td>
            <td>${item.analysis_date}</td>
            <td>${item.cd4}</td>
            <td>${item.abs_cd4}</td>
            <td>${item.wbc}</td>
          </tr>
        `);
                    });
                } else {
                    $('#historyTable tbody').append('<tr><td colspan="6" class="text-center">ไม่มีข้อมูล</td></tr>');
                }
                $('#historyModal').modal('show');
            }, 'json');
        });

        // ใช้ Select2 AJAX Search
        $('#patient_id').select2({
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

        // เมื่อเลือกผู้ป่วย
        $('#patient_id').on('select2:select', function(e) {
            var patientId = e.params.data.id;
            currentPatientId = patientId; // เก็บ id ไว้
            $.get('/lab_revenue/service/test_records/get_patient_info.php', {
                id: patientId
            }, function(data) {
                $('#patientInfo').show();
                $('#infoNap').text(data.nap_number);
                $('#infoHN').text(data.hn);
                $('#infoAge').text(data.age);
                $('#infoSex').text(data.sex);
            }, 'json');
        });

        // ฟอร์มบันทึกข้อมูล
        $('#labForm').submit(function(e) {
            e.preventDefault();
            $.post('/lab_revenue/service/test_records/insert_lab_test.php', $(this).serialize(), function(response) {
                Swal.fire('บันทึกสำเร็จ', response, 'success');
                $('#labForm')[0].reset();
                $('#patientInfo').hide();
            }).fail(function(xhr) {
                Swal.fire('ผิดพลาด', xhr.responseText, 'error');
            });
        });
    </script>

</body>

</html>
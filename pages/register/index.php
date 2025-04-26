<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/lab_revenue/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ODPC7 | Lab Revenue Register</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico">

    <!-- Important Stylesheets -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/css/adminlte.min.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Sweet Alert -->
    <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">


</head>

<body class="hold-transition register-page">
    <section class="content">
        <!-- register-box -->
        <div class="register-box">

            <div class="card card-primary mt-3 card-outline">
                <div class="card-header">
                    <h3 class="card-title">ลงทะเบียนเจ้าหน้าที่</h3>
                </div>
                <form id="staffRegisterForm">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="fname">ชื่อจริง</label>
                            <input type="text" name="fname" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="lname">นามสกุล</label>
                            <input type="text" name="lname" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="department">กลุ่มงาน</label>
                            <select name="department" id="department" class="form-control" required>
                                <option value="">-- เลือกกลุ่มงาน --</option>
                                <!-- โหลดจากฐานข้อมูลด้วย PHP -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="duty">ตำแหน่ง</label>
                            <select name="duty" id="duty" class="form-control" required>
                                <option value="">-- เลือกตำแหน่ง --</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="phone">เบอร์โทรศัพท์</label>
                            <input type="tel" name="phone" class="form-control" required pattern="0[0-9]{9}">
                        </div>
                        <div class="form-group">
                            <label for="email">อีเมล์</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="permission">สิทธิ์การใช้งาน</label>
                            <select name="permission" id="permission" class="form-control" required>
                                <option value="">-- เลือกสิทธิ์การใช้งาน --</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="username">ชื่อผู้ใช้ (Username)</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <!-- Password + Confirm -->
                        <div class="form-group">
                            <label>รหัสผ่าน</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" required minlength="8">
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password" style="cursor:pointer;"><i class="fas fa-eye"></i></span>
                                </div>
                            </div>
                            <small id="passwordStrengthText" class="form-text text-muted"></small>
                            <div class="progress mt-1">
                                <div id="passwordStrengthBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>ยืนยันรหัสผ่าน</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required equalTo="#password">
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">ลงทะเบียน</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- /.register-box -->

    <!-- Important Scripts -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/adminlte.min.js"></script>

    <!-- Local Script -->
    <script src="/lab_revenue/pages/register/script.js"></script>

    <!-- Form Validation -->
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="plugins/jquery-validation/additional-methods.min.js"></script>

    <!-- Sweet Alert -->
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            // 🔒 Toggle แสดง/ซ่อนรหัสผ่าน
            $('.toggle-password').click(function() {
                const input = $('#password');
                const icon = $(this).find('i');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // ✅ Password strength meter
            $('#password').on('input', function() {
                const password = $(this).val();
                const strength = checkStrength(password);
                $('#passwordStrengthText').text(strength.text);
                $('#passwordStrengthBar')
                    .removeClass()
                    .addClass('progress-bar')
                    .addClass(strength.class)
                    .css('width', strength.percent + '%');
            });

            function checkStrength(password) {
                let score = 0;
                if (password.length >= 8) score++;
                if (/[A-Z]/.test(password)) score++;
                if (/[0-9]/.test(password)) score++;
                if (/[^A-Za-z0-9]/.test(password)) score++;

                if (score <= 1) {
                    return {
                        percent: 25,
                        text: 'อ่อนมาก',
                        class: 'bg-danger'
                    };
                } else if (score == 2) {
                    return {
                        percent: 50,
                        text: 'ปานกลาง',
                        class: 'bg-warning'
                    };
                } else if (score == 3) {
                    return {
                        percent: 75,
                        text: 'ดี',
                        class: 'bg-info'
                    };
                } else {
                    return {
                        percent: 100,
                        text: 'แข็งแรงมาก',
                        class: 'bg-success'
                    };
                }
            }

            // ✨ jQuery Validation
            $('#staffRegisterForm').validate({
                rules: {
                    department: {
                        required: true
                    },
                    duty: {
                        required: true
                    },
                    permission: {
                        required: true
                    },
                    fname: {
                        required: true,
                        minlength: 2
                    },
                    lname: {
                        required: true,
                        minlength: 2
                    },
                    phone: {
                        required: true,
                        pattern: /^0[0-9]{9}$/ // 10 ตัวเลข เริ่มด้วย 0
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    username: {
                        required: true,
                        minlength: 4
                    },
                    password: {
                        required: true,
                        minlength: 8,
                        pwcheck: true
                    },
                    confirm_password: {
                        required: true,
                        equalTo: "#password"
                    }
                },
                messages: {
                    department: {
                        required: "กรุณาเลือกกลุ่มงาน"
                    },
                    duty: {
                        required: "กรุณาเลือกตำแหน่ง"
                    },
                    permission: {
                        required: "กรุณาเลือกสิทธิ์การใช้งาน"
                    },
                    fname: {
                        required: "กรุณากรอกชื่อจริง",
                        minlength: "ชื่อจริงต้องมีอย่างน้อย 2 ตัวอักษร"
                    },
                    lname: {
                        required: "กรุณากรอกนามสกุล",
                        minlength: "นามสกุลต้องมีอย่างน้อย 2 ตัวอักษร"
                    },
                    phone: {
                        required: "กรุณากรอกเบอร์โทรศัพท์",
                        pattern: "เบอร์โทรต้องขึ้นต้นด้วย 0 และมีทั้งหมด 10 หลัก"
                    },
                    email: {
                        required: "กรุณากรอกอีเมล",
                        email: "รูปแบบอีเมลไม่ถูกต้อง"
                    },
                    username: {
                        required: "กรุณากรอกชื่อผู้ใช้",
                        minlength: "ชื่อผู้ใช้ต้องมีอย่างน้อย 4 ตัวอักษร"
                    },
                    password: {
                        required: "กรุณากรอกรหัสผ่าน",
                        minlength: "รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร",
                        pwcheck: "รหัสผ่านต้องมีอักษรพิเศษ ตัวพิมพ์ใหญ่ และตัวเลข"
                    },
                    confirm_password: {
                        required: "กรุณายืนยันรหัสผ่าน",
                        equalTo: "กรุณากรอกรหัสผ่านให้ตรงกัน"
                    }
                },
                submitHandler: function(form) {
                    Swal.fire({
                        title: 'ยืนยันการลงทะเบียน?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'ตกลง',
                        cancelButtonText: 'ยกเลิก'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'service/register/register_staff.php',
                                method: 'POST',
                                data: $(form).serialize(),
                                success: function(res) {
                                    Swal.fire('สำเร็จ!', 'สมัครสมาชิกเรียบร้อยแล้ว', 'success');
                                    form.reset();
                                    $('#passwordStrengthBar').css('width', '0%');
                                    $('#passwordStrengthText').text('');
                                },
                                error: function() {
                                    Swal.fire('ผิดพลาด!', 'ไม่สามารถสมัครได้', 'error');
                                }
                            });
                        }
                    });
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });

            // ✨ Custom rule สำหรับ password
            $.validator.addMethod("pwcheck", function(value) {
                return /[A-Z]/.test(value) && /[0-9]/.test(value) && /[^A-Za-z0-9]/.test(value);
            });

            function loadSelectData(type, targetId) {
                $.get('service/register/get_selected_data.php', {
                    type
                }, function(data) {
                    const select = $('#' + targetId);
                    select.empty().append(`<option value="">-- กรุณาเลือก --</option>`);
                    data.forEach(item => {
                        select.append(`<option value="${item.id}">${item.name}</option>`);
                    });
                });
            }

            loadSelectData('department', 'department');
            loadSelectData('duty', 'duty');
            loadSelectData('permission', 'permission');
        });
    </script>
</body>

</html>
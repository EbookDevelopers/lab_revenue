<?php
function getBaseUrl() {
    return '/lab_revenue/';
}

function createUrl($path) {
    return getBaseUrl() . ltrim($path, '/');
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Login Page</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico">

  <!-- stylesheet -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit">
  <link rel="stylesheet" href="assets/css/adminlte.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
</head>

<body>
  <header class="bg"></header>
  <section class="d-flex align-items-center min-vh-100">
    <div class="container">
      <div class="row justify-content-center">
        <section class="col-lg-6">
          <div class="card shadow p-3 p-md-4">
            <h1 class="text-center text-primary font-weight-bold">Login Page</h1>
            <h4 class="text-center">เข้าสู่ระบบหลังบ้าน</h4>
            <div class="card-body">
              <!-- HTML Form Login -->
              <form id="loginForm">
                <div class="form-group col-sm-12">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text px-2">ชื่อผู้ใช้งาน</div>
                    </div>
                    <input type="text" class="form-control" name="username" placeholder="username">
                  </div>
                </div>
                <div class="form-group col-sm-12">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text px-3">รหัสผ่าน</div>
                    </div>
                    <input type="password" class="form-control" name="password" placeholder="password">
                  </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block"> เข้าสู่ระบบ</button>
              </form>
              <div class="row ml-auto">
                <div class="col-sm-12 text-right mt-2">
                  <a href="<?php echo createUrl('admin'); ?>" id="adminModeLink">Administrator Mode</a>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </section>

  <!-- script -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
  <script src="plugins/jquery-validation/additional-methods.min.js"></script>
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="plugins/sweetalert2/sweetalert2.min.js"></script>

  <script>
$('#loginForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '<?php echo createUrl("service/auth/check_staff_login"); ?>',
        method: 'POST',
        data: $(this).serialize(),
        success: function(res) {
            if (res === 'success') {
                location.href = '<?php echo createUrl("pages/dashboard/"); ?>';
            } else {
                Swal.fire('ผิดพลาด', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            console.log('Status:', status);
            console.log('Response:', xhr.responseText);
            Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์', 'error');
        }
    });
});


document.addEventListener('DOMContentLoaded', function() {
    var adminLink = document.getElementById('adminModeLink');
    
    if (adminLink) {
        adminLink.addEventListener('click', function(e) {
            // ลบ e.preventDefault() ออก และใช้การ redirect ปกติ
            window.location.href = this.href;
        });
    }
});

  </script>
</body>

</html>
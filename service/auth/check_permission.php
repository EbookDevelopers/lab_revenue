<?php
session_start();

function checkPermission($allowed_permissions = []) {
  if (!isset($_SESSION['staff_permission_id'])) {
    header("Location: /lab_revenue/login");
    exit;
  }

  if (!in_array($_SESSION['staff_permission_id'], $allowed_permissions)) {
    echo "<script>
      alert('คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
      window.location.href = 'unauthorized.php';
    </script>";
    exit;
  }
}

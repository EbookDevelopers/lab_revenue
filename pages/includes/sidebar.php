<?php
//session_start();
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['staff_id'])) {
    header('Location: /lab_revenue/login');
    exit;
}

require '../../service/connect.php';
function isActive($data)
{
    // ดึง path จาก REQUEST_URI
    $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    // แยก path ออกเป็น array
    $array = explode('/', $uri);

    // ตรวจสอบว่า $data ตรงกับส่วนสุดท้ายของ URL หรือไม่
    $name = end($array);
    return $name === $data ? 'active' : '';
}
?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars fa-2x"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto ">
        <li class="nav-item d-md-none d-block">
            <a href="../dashboard/">
                <img src="assets/images/AdminLogo.png"
                    alt="Admin Logo"
                    width="50px"
                    class="img-circle elevation-3">
                <span class="font-weight-light pl-1">Medical Revenue</span>
            </a>
        </li>
    </ul>
</nav>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="../dashboard/" class="brand-link">
        <img src="assets/images/AdminLogo.png"
            alt="Admin Logo"
            class="brand-image img-circle elevation-3">
        <span class="brand-text font-weight-light">Medical Revenue</span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="assets/images/avatar5.png" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="/lab_revenue/user" class="d-block"> <?php
                                                                if (isset($_SESSION['staff_id'])) {
                                                                    echo $_SESSION['staff_name'];
                                                                } else {
                                                                    echo $_SESSION['admin_name'];
                                                                }
                                                                ?> </a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="/lab_revenue/dashboard" class="nav-link <?php echo isActive('dashboard') ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>หน้าหลัก</p>
                    </a>
                </li>
                <?php if ((isset($_SESSION['staff_permission_id']) && in_array($_SESSION['staff_permission_id'], [1, 4])) || isset($_SESSION['admin_id'])): ?>
                    <li class="nav-item">
                        <a href="../test_records/index.php" class="nav-link <?php echo isActive('test_records') ?>">
                            <i class="nav-icon fas fa-notes-medical"></i>
                            <p>ข้อมูลการตรวจ</p>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ((isset($_SESSION['staff_permission_id']) && in_array($_SESSION['staff_permission_id'], [1, 2])) || isset($_SESSION['admin_id'])): ?>
                <li class="nav-item">
                    <a href="../debtors/index.php" class="nav-link <?php echo isActive('debtors') ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>ลูกหนี้</p>
                    </a>
                </li>
                <?php endif; ?>
                <?php if ((isset($_SESSION['staff_permission_id']) && in_array($_SESSION['staff_permission_id'], [1, 3])) || isset($_SESSION['admin_id'])): ?>
                <li class="nav-item">
                    <a href="../invoices/index.php" class="nav-link <?php echo isActive('invoices') ?>">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        <p>การแจ้งหนี้</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../billing/index.php" class="nav-link <?php echo isActive('billing') ?>">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>การชำระเงิน</p>
                    </a>
                </li>
                <?php endif; ?>
                <?php if ((isset($_SESSION['staff_permission_id']) && in_array($_SESSION['staff_permission_id'], [1, 4])) || isset($_SESSION['admin_id'])): ?>
                <li class="nav-item">
                    <a href="../debt_collection/index.php" class="nav-link <?php echo isActive('debt_collection') ?>">
                        <i class="nav-icon fas fa-phone"></i>
                        <p>การติดตามทวงถาม</p>
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a href="../report/index.php" class="nav-link <?php echo isActive('report') ?>">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>รายงาน</p>
                    </a>
                </li>
                <li class="nav-header">ตั้งค่าระบบบัญชีผู้ใช้งาน</li>
                <li class="nav-item">
                    <a href="/lab_revenue/user" class="nav-link <?php echo isActive('user') ?>">
                        <i class="nav-icon fas fa-user"></i>
                        <p>บัญชีผู้ใช้งาน</p>
                    </a>
                </li>
                <?php
                //session_start();
                if (isset($_SESSION['admin_id'])) { ?>
                    <li class="nav-item">
                        <a href="/lab_revenue/admin_dashboard" class="nav-link <?php echo isActive('admin_dashboard') ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashbard ผู้ดูแลระบบ</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/lab_revenue/admin" class="nav-link <?php echo isActive('admin') ?>">
                            <i class="nav-icon fas fa-user-cog"></i>
                            <p>จัดการข้อมูลผู้ใช้งาน</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/lab_revenue/login_stats" class="nav-link <?php echo isActive('login_stats') ?>">
                            <i class="nav-icon fas fa-book"></i>
                            <p>สถิติการ Login</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/lab_revenue/logs" class="nav-link <?php echo isActive('logs') ?>">
                            <i class="nav-icon fas fa-book"></i>
                            <p>ประวัติการเข้าใช้งาน</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/lab_revenue/setting" class="nav-link <?php echo isActive('setting') ?>">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>ตั้งค่าข้อมูลพื้นฐาน</p>
                        </a>
                    </li>
                <?php
                }
                ?>
                <li class="nav-item">
                    <a href="#" id="logoutBtn" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>ออกจากระบบ</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
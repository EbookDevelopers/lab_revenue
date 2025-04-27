<?php
// ตัดส่วน base path ออก
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/lab_revenue/';
$path = str_replace($base_path, '', $request_uri);
$path = strtok($path, '?'); // ตัดพารามิเตอร์ออก

// กรณีพิเศษสำหรับ alogin
if ($path == 'alogin') {
    include('alogin.php');
    exit;
}

// ถ้าไม่มี path หรือเป็น path ว่าง ให้ไปที่หน้า login
if (empty($path) || $path == '/' || $path == '') {
    include('login.php');
    exit;
}

// ตรวจสอบว่ามีไฟล์ที่ต้องการหรือไม่
if (!empty($path)) {
    $file_to_check = $path . '.php';
    if (file_exists($file_to_check)) {
        include($file_to_check);
        exit;
    }
}

// หากเป็น URL เดิมที่มี .php ให้ redirect ไปยัง URL ใหม่
if (preg_match('/(.+)\.php$/', $path, $matches)) {
    header('Location: ' . $base_path . $matches[1]);
    exit;
}

// ถ้าไม่พบไฟล์ ให้แสดงหน้า 404
header("HTTP/1.0 404 Not Found");
echo "404 - Page not found";
?>
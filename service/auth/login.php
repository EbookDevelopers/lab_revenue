<?php
//ไฟล์เชื่อมต่อฐานข้อมูล
require_once '../connect.php';

//ถ้ามีการส่ง input method post มาหน้านี้
if (isset($_POST['username']) && isset($_POST['password'])) {

    //รับค่าจาก input ที่ส่งมาจากฟอร์ม
    $username = $_POST['username'];
    $password = $_POST['password'];

    //กำหนด cost 10 เพื่อให้การเข้ารหัสรวดเร็วยิ่งขึ้น *ตัวเลขยิ่งเยอะ ยิ่งทำงานช้า ซึ่งขึ้นอยู่กับความเร็วของคอมที่เราใช้ครับ เพราะฉะนั้น 10 ก็พอครับ หรือจะลองเพิ่มตัวเลขแล้วรันดูครับ ว่าจะดีเลเยอะไหม!!
    $options = [
        'cost' => 10,
    ];

    //query เช็คว่า username ตรงไหม
    $sql = "SELECT username, password_hash FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);

    //ถ้า username ถูกต้อง จะเอามาเช็ค password ต่อว่า verify แล้วถ๔กต้องไหม
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_array($result);
        //รหัสผ่านมาจากตาราง
        $store_password = $row['password_hash'];
        //Verify  Password ตรวจสอบ password ระหว่าง $password และ $store_password
        $validPassword = password_verify($password, $store_password);

        if ($validPassword) {
            //verify password ผ่าน เข้าเงื่อนไขนี้

            //สร้าง query เพื่อเอาไปใช้งานต่อ
            $sql = "SELECT id, full_name, role, approved,created_at,username FROM users WHERE username='$username'";
            $query = mysqli_query($conn, $sql);
            $row = mysqli_fetch_array($query);
            //สร้างตัวแปร session

            $_SESSION['AD_ID'] = $row["id"];
            $_SESSION['AD_NAME'] = $row["full_name"];
            $_SESSION['AD_ROLE'] = $row["role"];
            $_SESSION['AD_USERNAME'] = $row["username"];
            //$_SESSION['AD_IMAGE'] = 'profile.jpg';
            $_SESSION['AD_STATUS'] = $row["approved"];
            $_SESSION['AD_LOGIN'] = date('Y-m-d H:i:s');

            $sqlUpdateLoginTime = "UPDATE users SET created_at = '" . $_SESSION['AD_LOGIN'] . "'WHERE id = '" . $_SESSION['AD_ID'] . "';";
            $queryUpdateLoginTime = mysqli_query($conn, $sqlUpdateLoginTime);

            //สร้างเงื่อนไขตรวจสอบระดับหรือสิทธิการใช้งาน
            if ($_SESSION["AD_STATUS"] != '0') {
                // ส่งผลลัพธ์กลับในรูปแบบ JSON
                echo json_encode([
                    'type' => 'success',
                    'message' => 'เข้าสู่ระบบสำเร็จ กำลัง Redirect ไปหน้าหลัก'
                ]);
            } else {
                echo json_encode([
                    'type' => 'error',
                    'message' => 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลส่วนนี้ได้หรือยังไม่ได้รับการอนุมัติจากผู้ดูแลระบบ'
                ]);
            }
        } else {
            // กรณี password ไม่ถูกต้อง
            session_destroy();
            echo json_encode([
                'type' => 'error',
                'message' => 'ชื่อผู้ใช้งาน/รหัสผ่านไม่ถูกต้อง'
            ]);
        }
    } else {
        // กรณีไม่พบชื่อผู้ใช้
        echo json_encode([
            'type' => 'error',
            'message' => 'ไม่พบชื่อผู้ใช้งานในระบบ'
        ]);
    }
}

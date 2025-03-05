<?php
$servername = "localhost";
$username = "root";
$password = "0889686118";
$dbname = "project";

// เปิดการรายงานข้อผิดพลาดของ mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
     $conn = new mysqli($servername, $username, $password, $dbname);
     $conn->set_charset("utf8"); // ตั้งค่าการเข้ารหัสเป็น UTF-8
} catch (Exception $e) {
     error_log($e->getMessage()); // บันทึกข้อผิดพลาดลง log
     exit('ไม่สามารถเชื่อมต่อฐานข้อมูลได้ในขณะนี้'); // แสดงข้อความที่ปลอดภัย
}
?>
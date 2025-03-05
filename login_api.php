<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $username = $_POST['username'];
     $password = $_POST['password'];

     // ค้นหาผู้ใช้ในฐานข้อมูล
     $stmt = $conn->prepare("SELECT user_id, password_hash FROM users WHERE username = ?");
     $stmt->bind_param("s", $username);
     $stmt->execute();
     $stmt->store_result();

     if ($stmt->num_rows > 0) {
          $stmt->bind_result($user_id, $password_hash);
          $stmt->fetch();

          if (password_verify($password, $password_hash)) {
               $_SESSION['user_id'] = $user_id;
               header("Location: index.php");
               exit();
          } else {
               $error = "รหัสผ่านไม่ถูกต้อง";
          }
     } else {
          $error = "ไม่พบบัญชีนี้";
     }
     $stmt->close();
}
?>
<?php
session_start();

$username = $_POST['username'];
$password = $_POST['password'];

// ตรวจสอบ username และ password
if ($username === 'admin' && $password === 'asd123456') {
     $_SESSION['username'] = $username;  // เก็บ session
     header("Location: ./vegetable/v1.php");  // เปลี่ยนเส้นทางไปยัง V1.php
     exit();
} else {
     echo "<script>alert('ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'); window.location.href = 'index.html';</script>";
}
?>
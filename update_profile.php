<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
     header("Location: login.php");
     exit;
}

// ตั้งค่าข้อความแจ้งเตือน
function setMessage($message, $type = 'danger')
{
     $_SESSION['message'] = $message;
     $_SESSION['message_type'] = $type;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $user_id = $_SESSION['user_id'];
     $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
     $full_name = htmlspecialchars($_POST['full_name']);
     $phone_number = htmlspecialchars($_POST['phone_number']);

     // เริ่ม transaction
     $conn->begin_transaction();

     try {
          // อัปเดตข้อมูลพื้นฐาน
          $sql = "UPDATE users SET email = ?, full_name = ?, phone_number = ? WHERE user_id = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("sssi", $email, $full_name, $phone_number, $user_id);
          $stmt->execute();

          // จัดการการเปลี่ยนรหัสผ่าน
          if (!empty($_POST['new_password'])) {
               if (empty($_POST['current_password'])) {
                    throw new Exception("กรุณากรอกรหัสผ่านปัจจุบัน");
               }

               if ($_POST['new_password'] !== $_POST['confirm_password']) {
                    throw new Exception("รหัสผ่านใหม่และการยืนยันรหัสผ่านไม่ตรงกัน");
               }

               // ตรวจสอบรหัสผ่านปัจจุบัน
               $stmt = $conn->prepare("SELECT password_hash FROM users WHERE user_id = ?");
               $stmt->bind_param("i", $user_id);
               $stmt->execute();
               $result = $stmt->get_result();
               $user = $result->fetch_assoc();

               if (!password_verify($_POST['current_password'], $user['password_hash'])) {
                    throw new Exception("รหัสผ่านปัจจุบันไม่ถูกต้อง");
               }

               // อัปเดตรหัสผ่านใหม่
               $new_password_hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
               $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
               $stmt->bind_param("si", $new_password_hash, $user_id);
               $stmt->execute();
          }

          // จัดการอัปโหลดรูปภาพแบบง่าย
          if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
               $upload_path = "img/U/" . str_pad($user_id, 5, '0', STR_PAD_LEFT) . "_1.jpg";

               // ตรวจสอบและสร้างโฟลเดอร์ถ้ายังไม่มี
               if (!is_dir("img/U/")) {
                    mkdir("img/U/", 0777, true);
               }

               // ย้ายไฟล์และแทนที่รูปเก่า
               if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                    throw new Exception("เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ");
               }
          }

          // ยืนยัน transaction
          $conn->commit();
          setMessage("อัปเดตข้อมูลเรียบร้อยแล้ว", "success");
          
          // รีเฟรชข้อมูล session
          $_SESSION['user_fullname'] = $full_name;
          $_SESSION['user_email'] = $email;
          
          // ทำให้เบราว์เซอร์ไม่แคชหน้าเว็บ
          header("Cache-Control: no-cache, must-revalidate");
          header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
          
          // เพิ่มพารามิเตอร์สุ่มเพื่อป้องกันการแคช
          header("Location: profile.php?t=" . time());
          exit;

     } catch (Exception $e) {
          // ยกเลิก transaction ถ้าเกิดข้อผิดพลาด
          $conn->rollback();
          setMessage($e->getMessage());
     }

     $conn->close();
     header("Location: profile.php");
     exit;
}

// ถ้าไม่ใช่ POST request ให้ redirect กลับไปหน้า profile
header("Location: profile.php");
exit;
?>
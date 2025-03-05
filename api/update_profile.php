<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
     http_response_code(401);
     echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
     exit;
}

include('../db.php');

try {
     $user_id = $_SESSION['user_id'];

     // Validate required fields
     if (empty($_POST['email']) || empty($_POST['full_name']) || empty($_POST['phone_number'])) {
          throw new Exception('กรุณากรอกข้อมูลให้ครบถ้วน');
     }

     // Start transaction
     $conn->begin_transaction();

     // Update user information
     $stmt = $conn->prepare("UPDATE users SET email = ?, full_name = ?, phone_number = ? WHERE user_id = ?");
     $stmt->bind_param(
          "sssi",
          $_POST['email'],
          $_POST['full_name'],
          $_POST['phone_number'],
          $user_id
     );

     if (!$stmt->execute()) {
          throw new Exception($stmt->error);
     }

     // Handle profile image if uploaded
     if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
          $upload_dir = "../img/U/";
          if (!is_dir($upload_dir)) {
               mkdir($upload_dir, 0777, true);
          }

          $filename = str_pad($user_id, 5, '0', STR_PAD_LEFT) . "_1.jpg";
          $upload_path = $upload_dir . $filename;

          if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
               throw new Exception('ไม่สามารถอัพโหลดรูปภาพได้');
          }
     }

     $conn->commit();
     echo json_encode([
          'success' => true,
          'message' => 'อัพเดทข้อมูลสำเร็จ'
     ]);

} catch (Exception $e) {
     if ($conn->connect_errno === 0) {
          $conn->rollback();
     }
     http_response_code(400);
     echo json_encode([
          'success' => false,
          'message' => $e->getMessage()
     ]);
}

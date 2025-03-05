<?php
session_start();
header('Content-Type: application/json');
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
     echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
     exit;
}

try {
     // รับและตรวจสอบข้อมูล
     $user_id = $_SESSION['user_id'];
     $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
     $content = $_POST['content'] ?? '';
     $link = $_POST['post_link'] ?? '';

     if (!$post_id || !$content) {
          throw new Exception('กรุณากรอกข้อมูลให้ครบถ้วน');
     }

     $conn->begin_transaction();

     // ตรวจสอบสิทธิ์และดึงข้อมูลโพสต์เดิม
     $stmt = $conn->prepare("SELECT * FROM posts WHERE post_id = ? AND user_id = ?");
     $stmt->bind_param("ii", $post_id, $user_id);
     $stmt->execute();
     $post = $stmt->get_result()->fetch_assoc();

     if (!$post) {
          throw new Exception('ไม่พบโพสต์หรือคุณไม่มีสิทธิ์แก้ไข');
     }

     // เตรียมข้อมูลสำหรับอัพเดต
     if ($link) {
          $content .= "\nลิงก์: " . $link;
     }

     // เริ่มสร้าง SQL query สำหรับอัพเดต
     $sql_parts = ["content = ?"];
     $params = [$content];
     $types = "s";

     // จัดการรูปภาพ
     $current_image = $post['image_path'];

     // กรณีอัพโหลดรูปใหม่
     if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === 0) {
          $target_dir = dirname(__DIR__) . "/img/posts/";
          if (!file_exists($target_dir)) {
               mkdir($target_dir, 0777, true);
          }

          $filename = time() . '_' . basename($_FILES['post_image']['name']);
          $target_file = $target_dir . $filename;

          if (move_uploaded_file($_FILES['post_image']['tmp_name'], $target_file)) {
               // ลบรูปเก่า
               if ($current_image) {
                    $old_file = dirname(__DIR__) . $current_image;
                    if (file_exists($old_file)) {
                         unlink($old_file);
                    }
               }

               $new_image_path = "/csP1015/project/img/posts/" . $filename;
               $sql_parts[] = "image_path = ?";
               $params[] = $new_image_path;
               $types .= "s";
          }
     }
     // กรณีต้องการลบรูปอย่างเดียว
     elseif (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
          if ($current_image) {
               $old_file = dirname(__DIR__) . $current_image;
               if (file_exists($old_file)) {
                    unlink($old_file);
               }
          }
          $sql_parts[] = "image_path = NULL";
     }

     // สร้าง SQL query สมบูรณ์
     $params[] = $post_id;
     $types .= "i";
     $sql = "UPDATE posts SET " . implode(", ", $sql_parts) . " WHERE post_id = ?";

     // อัพเดตข้อมูล
     $stmt = $conn->prepare($sql);
     $stmt->bind_param($types, ...$params);

     if (!$stmt->execute()) {
          throw new Exception("ไม่สามารถอัพเดตข้อมูลได้");
     }

     $conn->commit();

     // ดึงข้อมูลที่อัพเดตแล้ว
     $stmt = $conn->prepare("SELECT p.*, u.full_name FROM posts p JOIN users u ON p.user_id = u.user_id WHERE p.post_id = ?");
     $stmt->bind_param("i", $post_id);
     $stmt->execute();
     $updated_post = $stmt->get_result()->fetch_assoc();

     echo json_encode([
          'success' => true,
          'message' => 'อัพเดตโพสต์สำเร็จ',
          'data' => $updated_post
     ]);

} catch (Exception $e) {
     if ($conn->ping()) {
          $conn->rollback();
     }
     echo json_encode([
          'success' => false,
          'message' => $e->getMessage()
     ]);
     exit;
} finally {
     if (isset($stmt)) {
          $stmt->close();
     }
     if (isset($conn)) {
          $conn->close();
     }
}
?>
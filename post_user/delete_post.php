<?php
session_start();
require_once '../db.php';
header('Content-Type: application/json');

// Debug logging
error_log("Delete request received");
error_log("POST data: " . print_r($_POST, true));

if (!isset($_SESSION['user_id'])) {
     echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
     exit;
}

$user_id = $_SESSION['user_id'];
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

if (!$post_id) {
     echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสโพสต์']);
     exit;
}

try {
     // เริ่ม transaction
     $conn->begin_transaction();

     // ตรวจสอบว่าเป็นเจ้าของโพสต์
     $stmt = $conn->prepare("SELECT user_id FROM posts WHERE post_id = ? LIMIT 1");
     $stmt->bind_param("i", $post_id);
     $stmt->execute();
     $result = $stmt->get_result();
     $post = $result->fetch_assoc();

     if (!$post) {
          throw new Exception('ไม่พบโพสต์ที่ต้องการลบ');
     }

     if ($post['user_id'] != $user_id) {
          throw new Exception('คุณไม่มีสิทธิ์ลบโพสต์นี้');
     }

     // ลบไลค์
     $stmt = $conn->prepare("DELETE FROM likes WHERE post_id = ?");
     $stmt->bind_param("i", $post_id);
     $stmt->execute();

     // ลบคอมเมนต์
     $stmt = $conn->prepare("DELETE FROM comments WHERE post_id = ?");
     $stmt->bind_param("i", $post_id);
     $stmt->execute();

     // ลบโพสต์
     $stmt = $conn->prepare("DELETE FROM posts WHERE post_id = ? AND user_id = ?");
     $stmt->bind_param("ii", $post_id, $user_id);

     if (!$stmt->execute()) {
          throw new Exception('ไม่สามารถลบโพสต์ได้');
     }

     $conn->commit();
     echo json_encode(['success' => true]);

} catch (Exception $e) {
     $conn->rollback();
     error_log("Delete error: " . $e->getMessage());
     echo json_encode([
          'success' => false,
          'message' => $e->getMessage()
     ]);
} finally {
     $stmt->close();
     $conn->close();
}

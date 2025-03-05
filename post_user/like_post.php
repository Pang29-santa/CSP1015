<?php
header('Content-Type: application/json');
session_start();
require_once '../db.php';

// Debug logging
error_log("Like request received");
error_log("Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not set'));
error_log("POST data: " . print_r($_POST, true));

if (!isset($_SESSION['user_id'])) {
     echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบก่อน']);
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

     // ตรวจสอบว่าผู้ใช้เคยไลค์โพสต์นี้หรือไม่
     $check_stmt = $conn->prepare("SELECT post_id, user_id FROM likes WHERE post_id = ? AND user_id = ? LIMIT 1");
     $check_stmt->bind_param("ii", $post_id, $user_id);
     $check_stmt->execute();
     $result = $check_stmt->get_result();
     $existing_like = $result->fetch_assoc();

     if ($existing_like) {
          // กรณีเคยไลค์แล้ว ให้ยกเลิกไลค์
          $delete_stmt = $conn->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
          $delete_stmt->bind_param("ii", $post_id, $user_id);
          $delete_stmt->execute();
          $action = 'unliked';
     } else {
          // กรณียังไม่เคยไลค์ ให้เพิ่มไลค์
          $insert_stmt = $conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
          $insert_stmt->bind_param("ii", $post_id, $user_id);
          $insert_stmt->execute();
          $action = 'liked';
     }

     // นับจำนวนไลค์ทั้งหมดของโพสต์
     $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM likes WHERE post_id = ?");
     $count_stmt->bind_param("i", $post_id);
     $count_stmt->execute();
     $count_result = $count_stmt->get_result();
     $count = $count_result->fetch_assoc();

     // Commit transaction
     $conn->commit();

     // ส่งผลลัพธ์กลับ
     echo json_encode([
          'success' => true,
          'action' => $action,
          'likes_count' => $count['total']
     ]);

} catch (Exception $e) {
     // Rollback ในกรณีที่เกิดข้อผิดพลาด
     $conn->rollback();
     error_log("Like error: " . $e->getMessage());
     echo json_encode([
          'success' => false,
          'message' => 'เกิดข้อผิดพลาดในการดำเนินการ: ' . $e->getMessage()
     ]);
}

$conn->close();
?>
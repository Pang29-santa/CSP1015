<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
     echo json_encode(['success' => false, 'message' => 'Unauthorized']);
     exit;
}

try {
     require_once '../db.php';

     $input = json_decode(file_get_contents('php://input'), true);

     if (!isset($input['comment_id']) || !isset($input['comment_text'])) {
          throw new Exception('Missing required data');
     }

     $stmt = $conn->prepare("UPDATE comments SET comment_text = ?, updated_at = NOW() 
                           WHERE comment_id = ? AND user_id = ?");
     $stmt->bind_param("sii", $input['comment_text'], $input['comment_id'], $_SESSION['user_id']);

     if ($stmt->execute()) {
          if ($stmt->affected_rows > 0) {
               echo json_encode(['success' => true]);
          } else {
               echo json_encode(['success' => false, 'message' => 'No comment found or unauthorized']);
          }
     } else {
          throw new Exception('Error updating comment');
     }

} catch (Exception $e) {
     echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
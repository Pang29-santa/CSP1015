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

     if (!isset($input['comment_id'])) {
          throw new Exception('Missing comment ID');
     }

     $stmt = $conn->prepare("DELETE FROM comments WHERE comment_id = ? AND user_id = ?");
     $stmt->bind_param("ii", $input['comment_id'], $_SESSION['user_id']);

     if ($stmt->execute()) {
          if ($stmt->affected_rows > 0) {
               echo json_encode(['success' => true]);
          } else {
               echo json_encode(['success' => false, 'message' => 'No comment found or unauthorized']);
          }
     } else {
          throw new Exception('Error deleting comment');
     }

} catch (Exception $e) {
     echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
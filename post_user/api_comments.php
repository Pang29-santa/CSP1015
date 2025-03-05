<?php
session_start();
header('Content-Type: application/json');
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
     echo json_encode(['success' => false, 'message' => 'Unauthorized']);
     exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
     $post_id = $_GET['post_id'];

     if (isset($_GET['count_only'])) {
          $sql = "SELECT COUNT(*) as total FROM comments WHERE post_id = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("i", $post_id);
          $stmt->execute();
          $result = $stmt->get_result()->fetch_assoc();

          echo json_encode([
               'success' => true,
               'total' => $result['total']
          ]);
          exit;
     }

     $sql = "SELECT c.*, u.full_name, DATE_FORMAT(c.created_at, '%d/%m/%Y %H:%i') as formatted_date 
            FROM comments c 
            JOIN users u ON c.user_id = u.user_id 
            WHERE c.post_id = ? 
            ORDER BY c.created_at DESC";

     $stmt = $conn->prepare($sql);
     $stmt->bind_param("i", $post_id);
     $stmt->execute();
     $result = $stmt->get_result();

     $comments = [];
     while ($row = $result->fetch_assoc()) {
          $comments[] = $row;
     }

     echo json_encode([
          'success' => true,
          'comments' => $comments
     ]);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $data = json_decode(file_get_contents('php://input'), true);

     $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
     $stmt->bind_param("iis", $data['post_id'], $_SESSION['user_id'], $data['comment_text']);

     if ($stmt->execute()) {
          echo json_encode(['success' => true]);
     } else {
          echo json_encode(['success' => false, 'message' => 'Failed to add comment']);
     }
}

$conn->close();
?>
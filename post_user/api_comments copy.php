<?php
session_start();
header('Content-Type: application/json');
require_once '../db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Add at the beginning after header declarations
error_log('Received request: ' . $_SERVER['REQUEST_METHOD']);
error_log('GET params: ' . print_r($_GET, true));
error_log('POST data: ' . file_get_contents('php://input'));

$method = $_SERVER['REQUEST_METHOD'];
$user_id = $_SESSION['user_id'] ?? null;

// Log request details
error_log("Request Method: " . $method);
error_log("User ID: " . $user_id);
error_log("Request Data: " . file_get_contents("php://input"));

switch ($method) {
     case 'GET':
          if (isset($_GET['post_id'])) {
               getComments($_GET['post_id']);
          } else {
               echo json_encode([
                    "success" => false,
                    "message" => "Missing post_id parameter",
                    "debug" => $_GET
               ]);
          }
          break;

     case 'POST':
          // Add new comment
          addComment();
          break;

     case 'DELETE':
          // Delete comment
          if (isset($_GET['comment_id'])) {
               deleteComment($_GET['comment_id']);
          }
          break;

     // เพิ่มการจัดการ PUT method สำหรับแก้ไขความคิดเห็น
     case 'PUT':
          $data = json_decode(file_get_contents("php://input"), true);
          if (isset($data['comment_id']) && isset($data['comment_text'])) {
               $stmt = $conn->prepare("UPDATE comments SET comment_text = ? WHERE comment_id = ? AND user_id = ?");
               $stmt->bind_param("sii", $data['comment_text'], $data['comment_id'], $_SESSION['user_id']);

               echo json_encode([
                    "success" => $stmt->execute(),
                    "message" => $stmt->execute() ? "Updated successfully" : "Update failed"
               ]);
          }
          break;

     default:
          echo json_encode(["error" => "Method not allowed"]);
}

// แก้ไขฟังก์ชัน getComments ให้เรียงลำดับตาม created_at เสมอ
function getComments($post_id)
{
     global $conn;
     $userId = $_SESSION['user_id'] ?? 0;

     try {
          $sql = "SELECT 
            c.comment_id,
            c.post_id,
            c.user_id,
            c.comment_text,
            c.created_at,
            DATE_FORMAT(c.created_at, '%d/%m/%Y %H:%i') as formatted_date,
            u.username,
            u.full_name
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
               $row['is_owner'] = ($row['user_id'] == $userId);
               $comments[] = $row;
          }

          echo json_encode([
               "success" => true,
               "comments" => $comments,
               "total" => count($comments)
          ]);

     } catch (Exception $e) {
          echo json_encode([
               "success" => false,
               "message" => $e->getMessage()
          ]);
     }
}

function addComment()
{
     global $conn;
     $data = json_decode(file_get_contents("php://input"), true);

     try {
          if (!isset($_SESSION['user_id'])) {
               throw new Exception("Unauthorized - Please login first");
          }

          if (!isset($data['post_id']) || !isset($data['comment_text'])) {
               throw new Exception("Missing required fields");
          }

          $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
          $stmt->bind_param("iis", $data['post_id'], $_SESSION['user_id'], $data['comment_text']);

          if ($stmt->execute()) {
               $comment_id = $stmt->insert_id;

               // Fetch the newly added comment with all details
               $sql = "SELECT 
                    c.*,
                    u.username,
                    u.full_name,
                    DATE_FORMAT(c.created_at, '%d/%m/%Y %H:%i') as formatted_date
                    FROM comments c
                    JOIN users u ON c.user_id = u.user_id
                    WHERE c.comment_id = ?";

               $stmt = $conn->prepare($sql);
               $stmt->bind_param("i", $comment_id);
               $stmt->execute();
               $comment = $stmt->get_result()->fetch_assoc();

               echo json_encode([
                    "success" => true,
                    "message" => "Comment added successfully",
                    "comment" => $comment,
                    "debug" => [
                         "user_id" => $_SESSION['user_id'],
                         "timestamp" => date('Y-m-d H:i:s')
                    ]
               ]);
          }
     } catch (Exception $e) {
          error_log("Error in addComment: " . $e->getMessage());
          echo json_encode([
               "success" => false,
               "message" => $e->getMessage(),
               "debug" => [
                    "request_data" => $data,
                    "error" => $e->getMessage()
               ]
          ]);
     }
}

function deleteComment($comment_id)
{
     global $conn;

     if (!isset($_SESSION['user_id'])) {
          echo json_encode(["success" => false, "message" => "Unauthorized"]);
          return;
     }

     try {
          // Check if user owns the comment
          $stmt = $conn->prepare("SELECT user_id FROM comments WHERE comment_id = ?");
          $stmt->bind_param("i", $comment_id);
          $stmt->execute();
          $result = $stmt->get_result();
          $comment = $result->fetch_assoc();

          if (!$comment || $comment['user_id'] != $_SESSION['user_id']) {
               echo json_encode(["success" => false, "message" => "Unauthorized"]);
               return;
          }

          // Delete the comment
          $stmt = $conn->prepare("DELETE FROM comments WHERE comment_id = ?");
          $stmt->bind_param("i", $comment_id);

          if ($stmt->execute()) {
               echo json_encode([
                    "success" => true,
                    "message" => "Comment deleted successfully"
               ]);
          }
     } catch (Exception $e) {
          echo json_encode([
               "success" => false,
               "message" => $e->getMessage()
          ]);
     }
}
?>
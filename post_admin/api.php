<?php
header("Content-Type: application/json");
require_once '../db.php';

// Add image configuration
define('UPLOAD_DIR', dirname(__DIR__) . '/img/posts/');
define('IMAGE_URL_PATH', '/csP1015/project/img/posts/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
     case 'GET':
          if (isset($_GET['post_id'])) {
               getPostById($_GET['post_id']);
          } else {
               getAllPosts();
          }
          break;
     case 'POST':
          $data = json_decode(file_get_contents("php://input"), true);
          if (isset($data['action'])) {
               switch ($data['action']) {
                    case 'toggle_status':
                         togglePostStatus($data);
                         break;
                    case 'update_post':
                         updatePost();
                         break;
                    default:
                         addPost();
                         break;
               }
          } else {
               addPost();
          }
          break;
     case 'DELETE':
          $data = json_decode(file_get_contents("php://input"), true);
          if (isset($data['action']) && $data['action'] === 'delete_post') {
               deletePost($data['post_id']);
          }
          break;
     default:
          echo json_encode(["message" => "Method Not Allowed"]);
}

// ฟังก์ชันดึงโพสต์ทั้งหมด
function getAllPosts()
{
     global $conn;

     // Get pagination parameters
     $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
     $limit = 10; // Items per page
     $offset = ($page - 1) * $limit;

     // Get total count of all posts (including suspended)
     $countSql = "SELECT COUNT(*) as total FROM posts";
     $total = $conn->query($countSql)->fetch_assoc()['total'];
     $totalPages = ceil($total / $limit);

     // Get all posts with their status
     $sql = "SELECT p.*, u.username, u.full_name,
            COUNT(DISTINCT l.like_id) as likes_count,
            COUNT(DISTINCT c.comment_id) as comments_count
            FROM posts p
            JOIN users u ON p.user_id = u.user_id
            LEFT JOIN likes l ON p.post_id = l.post_id
            LEFT JOIN comments c ON p.post_id = c.post_id
            GROUP BY p.post_id
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?";

     $stmt = $conn->prepare($sql);
     $stmt->bind_param("ii", $limit, $offset);
     $stmt->execute();
     $result = $stmt->get_result();

     $posts = [];
     while ($row = $result->fetch_assoc()) {
          $posts[] = $row;
     }

     echo json_encode([
          'posts' => $posts,
          'pagination' => [
               'current_page' => $page,
               'total_pages' => $totalPages,
               'total_items' => $total
          ]
     ]);
}

// ฟังก์ชันดึงโพสต์เดี่ยว
function getPostById($post_id)
{
     global $conn;
     $sql = "SELECT p.*, u.username, u.full_name,
            COUNT(DISTINCT l.like_id) as likes_count,
            COUNT(DISTINCT c.comment_id) as comments_count
            FROM posts p
            JOIN users u ON p.user_id = u.user_id
            LEFT JOIN likes l ON p.post_id = l.post_id
            LEFT JOIN comments c ON p.post_id = c.post_id
            WHERE p.post_id = ?
            GROUP BY p.post_id";

     $stmt = $conn->prepare($sql);
     $stmt->bind_param("i", $post_id);
     $stmt->execute();
     $result = $stmt->get_result();

     if ($row = $result->fetch_assoc()) {
          // Fetch comments for this post
          $comments = getComments($post_id);
          $row['comments'] = $comments;
          echo json_encode($row);
     } else {
          echo json_encode(["message" => "Post not found"]);
     }
}

// ฟังก์ชันเพิ่มโพสต์ใหม่
function addPost()
{
     global $conn;
     $data = json_decode(file_get_contents("php://input"), true);

     if (!isset($data['user_id']) || !isset($data['content'])) {
          echo json_encode(["message" => "Missing required fields"]);
          return;
     }

     // Prepare SQL with image_path
     $sql = "INSERT INTO posts (user_id, content, image_path) VALUES (?, ?, ?)";
     $stmt = $conn->prepare($sql);

     // Get image path from request
     $image_path = isset($data['image_path']) ? $data['image_path'] : null;

     $stmt->bind_param("iss", $data['user_id'], $data['content'], $image_path);

     if ($stmt->execute()) {
          echo json_encode([
               "success" => true,
               "message" => "Post created successfully",
               "post_id" => $stmt->insert_id,
               "image_path" => $image_path
          ]);
     } else {
          echo json_encode([
               "success" => false,
               "message" => "Failed to create post: " . $conn->error
          ]);
     }
}

function handleImageUpload($base64_string, $user_id)
{
     // Check if directory exists, if not create it
     if (!file_exists(UPLOAD_DIR)) {
          mkdir(UPLOAD_DIR, 0777, true);
     }

     // Extract image information
     $image_parts = explode(";base64,", $base64_string);
     $image_type_aux = explode("image/", $image_parts[0]);
     $image_type = $image_type_aux[1];
     $image_base64 = base64_decode($image_parts[1]);

     // Generate unique filename
     $filename = $user_id . '_' . uniqid() . '.' . $image_type;
     $file_path = UPLOAD_DIR . $filename;

     // Save image
     if (file_put_contents($file_path, $image_base64)) {
          // Return relative path for database storage
          return IMAGE_URL_PATH . $filename;
     }

     return false;
}

// ฟังก์ชันอัปเดตโพสต์
function updatePost()
{
     global $conn;
     $data = json_decode(file_get_contents("php://input"), true);

     // Check if user owns the post
     $stmt = $conn->prepare("SELECT user_id FROM posts WHERE post_id = ?");
     $stmt->bind_param("i", $data['post_id']);
     $stmt->execute();
     $result = $stmt->get_result();
     $post = $result->fetch_assoc();

     if (!$post || $post['user_id'] != $data['user_id']) {
          echo json_encode(["success" => false, "message" => "Unauthorized"]);
          return;
     }

     if (!isset($data['post_id']) || !isset($data['content'])) {
          echo json_encode(["message" => "Missing required fields"]);
          return;
     }

     $image_path = null;
     if (isset($data['image']) && !empty($data['image'])) {
          // Delete old image if exists
          $sql = "SELECT image_path FROM posts WHERE post_id = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("i", $data['post_id']);
          $stmt->execute();
          $result = $stmt->get_result();
          if ($old_image = $result->fetch_assoc()) {
               if ($old_image['image_path']) {
                    @unlink('../' . $old_image['image_path']);
               }
          }

          // Upload new image
          $image_path = handleImageUpload($data['image'], $data['user_id']);
     }

     // Update post
     $sql = $image_path
          ? "UPDATE posts SET content = ?, image_path = ? WHERE post_id = ?"
          : "UPDATE posts SET content = ? WHERE post_id = ?";

     $stmt = $conn->prepare($sql);
     if ($image_path) {
          $stmt->bind_param("ssi", $data['content'], $image_path, $data['post_id']);
     } else {
          $stmt->bind_param("si", $data['content'], $data['post_id']);
     }

     if ($stmt->execute()) {
          echo json_encode([
               "success" => true,
               "message" => "Post updated successfully",
               "image_path" => $image_path
          ]);
     } else {
          echo json_encode(["success" => false, "message" => "Failed to update post"]);
     }
}

// ฟังก์ชันลบโพสต์
function deletePost($post_id)
{
     global $conn;

     try {
          // Start transaction
          $conn->begin_transaction();

          // Check if post exists and is suspended
          $stmt = $conn->prepare("SELECT status, image_path FROM posts WHERE post_id = ?");
          $stmt->bind_param("i", $post_id);
          $stmt->execute();
          $result = $stmt->get_result();
          $post = $result->fetch_assoc();

          if (!$post) {
               throw new Exception("โพสต์ไม่พบในระบบ");
          }

          if ($post['status'] !== 'suspended') {
               throw new Exception("สามารถลบได้เฉพาะโพสต์ที่ถูกระงับเท่านั้น");
          }

          // Delete related records
          $stmt = $conn->prepare("DELETE FROM likes WHERE post_id = ?");
          $stmt->bind_param("i", $post_id);
          $stmt->execute();

          $stmt = $conn->prepare("DELETE FROM comments WHERE post_id = ?");
          $stmt->bind_param("i", $post_id);
          $stmt->execute();

          // Delete the post
          $stmt = $conn->prepare("DELETE FROM posts WHERE post_id = ?");
          $stmt->bind_param("i", $post_id);

          if (!$stmt->execute()) {
               throw new Exception("ไม่สามารถลบโพสต์ได้");
          }

          // Delete image if exists
          if ($post['image_path']) {
               $image_path = dirname(__DIR__) . $post['image_path'];
               if (file_exists($image_path)) {
                    unlink($image_path);
               }
          }

          // Commit transaction
          $conn->commit();

          echo json_encode([
               "success" => true,
               "message" => "ลบโพสต์สำเร็จ"
          ]);

     } catch (Exception $e) {
          // Rollback on error
          $conn->rollback();
          echo json_encode([
               "success" => false,
               "message" => $e->getMessage()
          ]);
     }
}

function handleLike()
{
     global $conn;
     $data = json_decode(file_get_contents("php://input"), true);

     if (!isset($data['post_id']) || !isset($data['user_id'])) {
          echo json_encode(["message" => "Missing required fields"]);
          return;
     }

     // Check if already liked
     $check_sql = "SELECT like_id FROM likes WHERE post_id = ? AND user_id = ?";
     $check_stmt = $conn->prepare($check_sql);
     $check_stmt->bind_param("ii", $data['post_id'], $data['user_id']);
     $check_stmt->execute();
     $result = $check_stmt->get_result();

     if ($result->num_rows > 0) {
          // Unlike
          $sql = "DELETE FROM likes WHERE post_id = ? AND user_id = ?";
     } else {
          // Like
          $sql = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
     }

     $stmt = $conn->prepare($sql);
     $stmt->bind_param("ii", $data['post_id'], $data['user_id']);

     if ($stmt->execute()) {
          echo json_encode(["message" => "Like updated successfully"]);
     } else {
          echo json_encode(["message" => "Failed to update like"]);
     }
}

function getComments($post_id)
{
     global $conn;
     $sql = "SELECT c.*, u.username, u.full_name, DATE_FORMAT(c.created_at, '%d/%m/%Y %H:%i') as formatted_date
            FROM comments c
            JOIN users u ON c.user_id = u.user_id
            WHERE c.post_id = ?
            ORDER BY c.created_at ASC";

     $stmt = $conn->prepare($sql);
     $stmt->bind_param("i", $post_id);
     $stmt->execute();
     $result = $stmt->get_result();

     $comments = [];
     while ($row = $result->fetch_assoc()) {
          $comments[] = $row;
     }
     return $comments;
}

function addComment()
{
     global $conn;
     $data = json_decode(file_get_contents("php://input"), true);

     if (!isset($data['post_id']) || !isset($data['user_id']) || !isset($data['comment_text'])) {
          echo json_encode(["success" => false, "message" => "Missing required fields"]);
          return;
     }

     $sql = "INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)";
     $stmt = $conn->prepare($sql);
     $stmt->bind_param("iis", $data['post_id'], $data['user_id'], $data['comment_text']);

     if ($stmt->execute()) {
          // Fetch the newly added comment with user info
          $comment_id = $stmt->insert_id;
          $sql = "SELECT c.*, u.username, u.full_name, DATE_FORMAT(c.created_at, '%d/%m/%Y %H:%i') as formatted_date
                FROM comments c
                JOIN users u ON c.user_id = u.user_id
                WHERE c.comment_id = ?";

          $stmt = $conn->prepare($sql);
          $stmt->bind_param("i", $comment_id);
          $stmt->execute();
          $result = $stmt->get_result();
          $comment = $result->fetch_assoc();

          echo json_encode([
               "success" => true,
               "message" => "Comment added successfully",
               "comment" => $comment
          ]);
     } else {
          echo json_encode([
               "success" => false,
               "message" => "Failed to add comment"
          ]);
     }
}

// Add new function for toggling status
function togglePostStatus($data)
{
     global $conn;

     if (!isset($data['post_id']) || !isset($data['status'])) {
          echo json_encode(["success" => false, "message" => "Missing required fields"]);
          return;
     }

     if (!in_array($data['status'], ['active', 'suspended'])) {
          echo json_encode(["success" => false, "message" => "Invalid status"]);
          return;
     }

     $sql = "UPDATE posts SET status = ? WHERE post_id = ?";
     $stmt = $conn->prepare($sql);
     $stmt->bind_param("si", $data['status'], $data['post_id']);

     if ($stmt->execute()) {
          $message = $data['status'] === 'active' ? 'ปลดล็อกโพสต์สำเร็จ' : 'ระงับโพสต์สำเร็จ';
          echo json_encode([
               "success" => true,
               "message" => $message
          ]);
     } else {
          echo json_encode([
               "success" => false,
               "message" => "ไม่สามารถอัพเดทสถานะโพสต์ได้"
          ]);
     }
}
?>
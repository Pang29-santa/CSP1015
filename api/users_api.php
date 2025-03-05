<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

include('../db.php');

$response = array();
$method = $_SERVER['REQUEST_METHOD'];

try {
     switch ($method) {
          case 'GET':
               if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                    handleGetUser(intval($_GET['id']));
               } else {
                    handleGetUsers();
               }
               break;

          case 'POST':
               if (empty($_POST['id'])) {
                    handleCreateUser();
               } else {
                    handleUpdateUser();
               }
               break;

          case 'PUT':
               handlePutUser();
               break;

          case 'DELETE':
               handleDeleteUser();
               break;

          default:
               throw new Exception("Method not allowed");
     }
} catch (Exception $e) {
     http_response_code(400);
     echo json_encode([
          'success' => false,
          'message' => $e->getMessage()
     ], JSON_UNESCAPED_UNICODE);
}

function handleGetUser($user_id)
{
     global $conn;
     $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
     $stmt->bind_param("i", $user_id);
     $stmt->execute();
     $result = $stmt->get_result();

     if ($result->num_rows > 0) {
          $user = $result->fetch_assoc();
          // ลบข้อมูลที่ sensitive ออก
          unset($user['pass']);

          // เพิ่มข้อมูลรูปภาพ
          $imageFilename = str_pad($user['user_id'], 5, '0', STR_PAD_LEFT) . "_1.jpg";
          $imagePath = "img/U/" . $imageFilename;
          $user['profile_image'] = file_exists($imagePath) ? $imagePath : "img/default-profile.jpg";

          echo json_encode([
               'success' => true,
               'data' => $user
          ], JSON_UNESCAPED_UNICODE);
     } else {
          throw new Exception("ไม่พบข้อมูลผู้ใช้");
     }
}

function handleGetUsers()
{
     global $conn;
     $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
     $limit = 10;
     $offset = ($page - 1) * $limit;

     // คำสั่ง SQL สำหรับดึงข้อมูลผู้ใช้แบบแบ่งหน้า
     $stmt = $conn->prepare("SELECT user_id, username, full_name, email, phone_number FROM users LIMIT ? OFFSET ?");
     $stmt->bind_param("ii", $limit, $offset);
     $stmt->execute();
     $result = $stmt->get_result();

     $users = [];
     while ($row = $result->fetch_assoc()) {
          $imageFilename = str_pad($row['user_id'], 5, '0', STR_PAD_LEFT) . "_1.jpg";
          $imagePath = "img/U/" . $imageFilename;
          $row['profile_image'] = file_exists($imagePath) ? $imagePath : "img/default-profile.jpg";
          $users[] = $row;
     }

     // นับจำนวนผู้ใช้ทั้งหมด
     $total = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
     $total_pages = ceil($total / $limit);

     echo json_encode([
          'success' => true,
          'data' => $users,
          'pagination' => [
               'current_page' => $page,
               'total_pages' => $total_pages,
               'total_items' => $total,
               'limit' => $limit
          ]
     ], JSON_UNESCAPED_UNICODE);
}

// ...ฟังก์ชันอื่นๆ สำหรับ handleCreateUser, handleUpdateUser, handlePutUser, handleDeleteUser...

$conn->close();

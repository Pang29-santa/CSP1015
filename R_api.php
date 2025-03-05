<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

include('db.php');

$response = array();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
     case 'GET':
          if (isset($_GET['id']) && is_numeric($_GET['id'])) {
               // ค้นหาข้อมูลผู้ใช้ตาม ID
               $user_id = intval($_GET['id']);
               $sql = "SELECT * FROM users WHERE user_id = ?";
               $stmt = $conn->prepare($sql);
               $stmt->bind_param("i", $user_id);
               $stmt->execute();
               $result = $stmt->get_result();

               if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $imageFilename = str_pad($row['user_id'], 5, '0', STR_PAD_LEFT) . "_1.jpg";
                    $imagePath = "http://localhost:8080/csP1015/project/img/U/" . $imageFilename;
                    $imageLocalPath = __DIR__ . "img/U/" . $imageFilename;
                    $imageAvailable = file_exists($imageLocalPath) ? $imagePath : "ไม่พบรูปภาพ";

                    $response = array(
                         "id" => $row['user_id'],
                         "username" => $row['username'],
                         "email" => $row['email'],
                         "full_name" => $row['full_name'],
                         "phone_number" => $row['phone_number'],
                         "image" => $imageAvailable
                    );
               } else {
                    $response = array("message" => "ไม่พบข้อมูลผู้ใช้ที่ระบุ");
               }
          } else {
               // คำนวณหน้าและการแสดงผล
               $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
               $limit = 5;
               $offset = ($page - 1) * $limit;

               $sql = "SELECT * FROM users LIMIT ? OFFSET ?";
               $stmt = $conn->prepare($sql);
               $stmt->bind_param("ii", $limit, $offset);
               $stmt->execute();
               $result = $stmt->get_result();

               $users = array();
               while ($row = $result->fetch_assoc()) {
                    $imageFilename = str_pad($row['user_id'], 5, '0', STR_PAD_LEFT) . "_1.jpg";
                    $imagePath = "http://localhost:8080/csP1015/project/img/U/" . $imageFilename;
                    $imageLocalPath = __DIR__ . "img/U/" . $imageFilename;
                    $imageAvailable = file_exists($imageLocalPath) ? $imagePath : "ไม่พบรูปภาพ";

                    $users[] = array(
                         "id" => $row['user_id'],
                         "username" => $row['username'],
                         "full_name" => $row['full_name'],
                         "image" => $imageAvailable
                    );
               }

               $sqlCount = "SELECT COUNT(*) AS total FROM users";
               $resultCount = $conn->query($sqlCount);
               $rowCount = $resultCount->fetch_assoc();
               $totalRows = $rowCount['total'];
               $totalPages = ceil($totalRows / $limit);

               $response = array(
                    "users" => $users,
                    "pagination" => array(
                         "total_items" => $totalRows,
                         "total_pages" => $totalPages,
                         "current_page" => $page
                    )
               );
          }
          echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
          break;

     case 'POST':
          if (empty($_POST['id'])) {
               // เพิ่มผู้ใช้ใหม่
               if (isset($_POST['username'], $_POST['password'], $_POST['email'], $_POST['full_name'], $_POST['phone_number'])) {
                    // ตรวจสอบว่ามี username ซ้ำหรือไม่
                    $check_sql = "SELECT COUNT(*) as count FROM users WHERE username = ?";
                    $check_stmt = $conn->prepare($check_sql);
                    $check_stmt->bind_param("s", $_POST['username']);
                    $check_stmt->execute();
                    $result = $check_stmt->get_result();
                    $row = $result->fetch_assoc();

                    if ($row['count'] > 0) {
                         echo json_encode(array("status" => "error", "message" => "ชื่อผู้ใช้นี้มีอยู่ในระบบแล้ว"), JSON_UNESCAPED_UNICODE);
                         exit;
                    }

                    $username = $_POST['username'];
                    $password = $_POST['password'];
                    $email = $_POST['email'];
                    $full_name = $_POST['full_name'];
                    $phone_number = $_POST['phone_number'];
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);

                    $sql = "INSERT INTO users (username, password_hash, email, full_name, phone_number) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssss", $username, $password_hash, $email, $full_name, $phone_number);

                    if ($stmt->execute()) {
                         $user_id = $stmt->insert_id;

                         // จัดการอัปโหลดรูปภาพ
                         if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                              $upload_dir = "img/U/";
                              if (!is_dir($upload_dir)) {
                                   mkdir($upload_dir, 0777, true);
                              }

                              $imagePath = $upload_dir . str_pad($user_id, 5, '0', STR_PAD_LEFT) . "_1.jpg";
                              if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                                   header('Location: login.php');
                                   exit;
                              }
                         }

                         header('Location: login.php');
                         exit;
                    } else {
                         echo json_encode(array("status" => "error", "message" => "เกิดข้อผิดพลาดในการบันทึกข้อมูล"), JSON_UNESCAPED_UNICODE);
                    }
               } else {
                    echo json_encode(array("status" => "error", "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"), JSON_UNESCAPED_UNICODE);
               }
          } else {
               // อัปเดตข้อมูลผู้ใช้
               $id = $_POST['id'];
               $username = $_POST['username'];
               $email = $_POST['email'];
               $full_name = $_POST['full_name'];
               $phone_number = $_POST['phone_number'];

               $sql = "UPDATE users SET username = ?, email = ?, full_name = ?, phone_number = ?";
               $params = array($username, $email, $full_name, $phone_number);
               $types = "ssss";

               if (!empty($_POST['password'])) {
                    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $sql .= ", password_hash = ?";
                    $params[] = $password_hash;
                    $types .= "s";
               }

               $sql .= " WHERE user_id = ?";
               $params[] = $id;
               $types .= "i";

               $stmt = $conn->prepare($sql);
               $stmt->bind_param($types, ...$params);

               if ($stmt->execute()) {
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                         $imagePath = "img/U/" . str_pad($id, 5, '0', STR_PAD_LEFT) . "_1.jpg";
                         move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
                    }
                    header('Location: login.php');
                    exit;
               } else {
                    echo json_encode(array("status" => "error", "message" => "เกิดข้อผิดพลาดในการอัปเดตข้อมูล"), JSON_UNESCAPED_UNICODE);
               }
          }
          break;

     case 'PUT':
          $data = json_decode(file_get_contents("php://input"), true);
          if (isset($data['id'], $data['username'], $data['email'], $data['full_name'], $data['phone_number'])) {
               $id = $data['id'];
               $username = $data['username'];
               $email = $data['email'];
               $full_name = $data['full_name'];
               $phone_number = $data['phone_number'];

               $sql = "UPDATE users SET username = ?, email = ?, full_name = ?, phone_number = ? WHERE user_id = ?";
               $stmt = $conn->prepare($sql);
               $stmt->bind_param("ssssi", $username, $email, $full_name, $phone_number, $id);

               if ($stmt->execute()) {
                    $response = array("status" => "success", "message" => "อัปเดตข้อมูลผู้ใช้สำเร็จ");
               } else {
                    $response = array("status" => "error", "message" => "เกิดข้อผิดพลาดในการอัปเดตข้อมูล");
               }
          } else {
               $response = array("status" => "error", "message" => "ข้อมูลไม่ครบถ้วน");
          }
          echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
          break;

     case 'DELETE':
          $json = file_get_contents("php://input");
          $data = json_decode($json, true);

          if (isset($data['id'])) {
               $user_id = intval($data['id']);
               $sql = "DELETE FROM users WHERE user_id = ?";

               if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();

                    if ($stmt->affected_rows > 0) {
                         // ลบรูปภาพ (ถ้ามี)
                         $imagePath = "img/U/" . str_pad($user_id, 5, '0', STR_PAD_LEFT) . "_1.jpg";
                         if (file_exists($imagePath)) {
                              unlink($imagePath);
                         }

                         $response = array("status" => "success", "message" => "ลบข้อมูลสำเร็จ");
                    } else {
                         $response = array("status" => "error", "message" => "ไม่พบข้อมูลที่ต้องการลบ");
                    }
                    $stmt->close();
               } else {
                    $response = array("status" => "error", "message" => "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL");
               }
          } else {
               $response = array("status" => "error", "message" => "ไม่ได้ระบุ ID");
          }
          echo json_encode($response, JSON_UNESCAPED_UNICODE);
          break;
}

$conn->close();
?>
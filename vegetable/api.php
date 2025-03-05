<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

include('../db.php');

$response = array();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
     case 'GET':
          if (isset($_GET['id']) && is_numeric($_GET['id'])) {
               // ค้นหาข้อมูลผักตาม ID
               $vegetable_id = intval($_GET['id']);
               $sql = "SELECT * FROM vegetable WHERE vegetable_id = ?";
               $stmt = $conn->prepare($sql);
               $stmt->bind_param("i", $vegetable_id);
               $stmt->execute();
               $result = $stmt->get_result();

               if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $imageFilename = str_pad($row['vegetable_id'], 5, '0', STR_PAD_LEFT) . "_1.jpg";
                    $imagePath = "http://localhost:8080/csP1015/project/img/V/" . $imageFilename;
                    $imageLocalPath = __DIR__ . "/../img/V/" . $imageFilename;
                    $imageAvailable = file_exists($imageLocalPath) ? $imagePath : "ไม่พบรูปภาพ";

                    $response = array(
                         "id" => $row['vegetable_id'],
                         "thai_name" => $row['thai_name'],
                         "eng_name" => $row['eng_name'],
                         "sci_name" => $row['sci_name'],
                         "growth" => $row['growth'],
                         "planting_method" => $row['planting_method'],
                         "care" => $row['care'],
                         "details" => $row['details'],
                         "image" => $imageAvailable
                    );
               } else {
                    $response = array("message" => "ไม่พบข้อมูลผักที่ระบุ");
               }
               echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
          } else {
               // คำนวณหน้าและการแสดงผล
               $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
               $limit = 5; // จำนวนรายการที่แสดงต่อหน้า
               $offset = ($page - 1) * $limit; // คำนวณค่า offset

               // ค้นหาข้อมูลผักทั้งหมด 5 รายการต่อหน้า
               $sql = "SELECT * FROM vegetable LIMIT ? OFFSET ?";
               $stmt = $conn->prepare($sql);
               $stmt->bind_param("ii", $limit, $offset);
               $stmt->execute();
               $result = $stmt->get_result();

               $vegetables = array();
               while ($row = $result->fetch_assoc()) {
                    $imageFilename = str_pad($row['vegetable_id'], 5, '0', STR_PAD_LEFT) . "_1.jpg";
                    $imagePath = "http://localhost:8080/csP1015/project/img/V/" . $imageFilename;
                    $imageLocalPath = __DIR__ . "/../img/V/" . $imageFilename;
                    $imageAvailable = file_exists($imageLocalPath) ? $imagePath : "ไม่พบรูปภาพ";

                    $vegetables[] = array(
                         "id" => $row['vegetable_id'],
                         "thai_name" => $row['thai_name'],
                         "eng_name" => $row['eng_name'],
                         "image" => $imageAvailable
                    );
               }

               // คำนวณจำนวนทั้งหมด
               $sqlCount = "SELECT COUNT(*) AS total FROM vegetable";
               $resultCount = $conn->query($sqlCount);
               $rowCount = $resultCount->fetch_assoc();
               $totalRows = $rowCount['total'];

               // คำนวณจำนวนหน้าทั้งหมด
               $totalPages = ceil($totalRows / $limit);

               $response["vegetables"] = $vegetables;
               $response["pagination"] = array(
                    "total_items" => $totalRows,
                    "total_pages" => $totalPages,
                    "current_page" => $page
               );
               echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
          }
          break;

     case 'POST':
          if (isset($_POST['id']) && !empty($_POST['id'])) {
               // Update existing vegetable
               $id = $_POST['id'];
               $thai_name = $_POST['thai_name'];
               $eng_name = $_POST['eng_name'];
               $sci_name = $_POST['sci_name'];
               $growth = $_POST['growth'];
               $planting_method = $_POST['planting_method'];
               $care = $_POST['care'];
               $details = $_POST['details'];

               if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                    $imagePath = "../img/V/" . str_pad($id, 5, '0', STR_PAD_LEFT) . "_1.jpg";
                    move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
               }

               $sql = "UPDATE vegetable SET thai_name = ?, eng_name = ?, sci_name = ?, growth = ?, planting_method = ?, care = ?, details = ? WHERE vegetable_id = ?";
               $stmt = $conn->prepare($sql);
               $stmt->bind_param("sssssssi", $thai_name, $eng_name, $sci_name, $growth, $planting_method, $care, $details, $id);

               if ($stmt->execute()) {
                    $response = ["message" => "แก้ไขข้อมูลสำเร็จ"];
               } else {
                    $response = ["message" => "เกิดข้อผิดพลาดในการแก้ไขข้อมูล"];
               }
          } else {
               // Add new vegetable
               if (isset($_POST['thai_name'], $_POST['eng_name'], $_POST['sci_name'], $_POST['growth'], $_POST['planting_method'], $_POST['care'], $_POST['details'])) {
                    $thai_name = $_POST['thai_name'];
                    $eng_name = $_POST['eng_name'];
                    $sci_name = $_POST['sci_name'];
                    $growth = $_POST['growth'];
                    $planting_method = $_POST['planting_method'];
                    $care = $_POST['care'];
                    $details = $_POST['details'];

                    $sql = "INSERT INTO vegetable (thai_name, eng_name, sci_name, growth, planting_method, care, details) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssss", $thai_name, $eng_name, $sci_name, $growth, $planting_method, $care, $details);

                    if ($stmt->execute()) {
                         $new_id = $conn->insert_id;
                         if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                              $imagePath = "../img/V/" . str_pad($new_id, 5, '0', STR_PAD_LEFT) . "_1.jpg";
                              move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
                         }
                         $response = ["message" => "เพิ่มข้อมูลสำเร็จ", "id" => $new_id];
                    } else {
                         $response = ["message" => "เกิดข้อผิดพลาดในการเพิ่มข้อมูล"];
                    }
               } else {
                    $response = ["message" => "ข้อมูลไม่ครบถ้วน"];
               }
          }
          echo json_encode($response, JSON_UNESCAPED_UNICODE);
          break;

     case 'PUT':
          // แก้ไขข้อมูลผัก
          $data = json_decode(file_get_contents("php://input"), true);
          if (isset($data['id'], $data['thai_name'], $data['eng_name'], $data['sci_name'], $data['growth'], $data['planting_method'], $data['care'], $data['details'])) {
               $id = $data['id'];
               $thai_name = $data['thai_name'];
               $eng_name = $data['eng_name'];
               $sci_name = $data['sci_name'];
               $growth = $data['growth'];
               $planting_method = $data['planting_method'];
               $care = $data['care'];
               $details = $data['details'];

               $sql = "UPDATE vegetable SET thai_name = ?, eng_name = ?, sci_name = ?, growth = ?, planting_method = ?, care = ?, details = ? WHERE vegetable_id = ?";
               $stmt = $conn->prepare($sql);
               $stmt->bind_param("sssssssi", $thai_name, $eng_name, $sci_name, $growth, $planting_method, $care, $details, $id);

               if ($stmt->execute()) {
                    $response = array("message" => "แก้ไขข้อมูลผักสำเร็จ");
               } else {
                    $response = array("message" => "เกิดข้อผิดพลาดในการแก้ไขข้อมูล");
               }
               echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
          } else {
               $response = array("message" => "ข้อมูลไม่ครบถ้วน");
               echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
          }
          break;

     case 'DELETE':
          if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
               // รับ JSON input
               $json = file_get_contents("php://input");
               error_log("JSON ที่ได้รับ: " . $json); // ตรวจสอบ JSON ที่ส่งมา

               $data = json_decode($json, true);

               if (isset($data['id'])) {
                    $vegetable_id = intval($data['id']);
                    error_log("ID ที่จะลบ: " . $vegetable_id); // ตรวจสอบค่า ID

                    $sql = "DELETE FROM vegetable WHERE vegetable_id = ?";
                    if ($stmt = $conn->prepare($sql)) {
                         $stmt->bind_param("i", $vegetable_id);
                         $stmt->execute();

                         if ($stmt->affected_rows > 0) {
                              echo json_encode(["message" => "ลบข้อมูลสำเร็จ"], JSON_UNESCAPED_UNICODE);
                         } else {
                              echo json_encode(["message" => "ไม่พบข้อมูลที่ต้องการลบ"], JSON_UNESCAPED_UNICODE);
                         }

                         $stmt->close();
                    } else {
                         echo json_encode(["message" => "ข้อผิดพลาด SQL: " . $conn->error], JSON_UNESCAPED_UNICODE);
                    }
               } else {
                    echo json_encode(["message" => "ไม่มี ID ส่งมา"], JSON_UNESCAPED_UNICODE);
               }
          } else {
               echo json_encode(["message" => "Method ไม่ถูกต้อง"], JSON_UNESCAPED_UNICODE);
          }
          break;

}

$conn->close();
?>
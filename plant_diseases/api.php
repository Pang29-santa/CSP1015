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
               // ค้นหาข้อมูลโรคตาม ID
               $disease_id = intval($_GET['id']);
               $sql = "SELECT * FROM diseases WHERE disease_id = ?";
               $stmt = $conn->prepare($sql);
               $stmt->bind_param("i", $disease_id);
               $stmt->execute();
               $result = $stmt->get_result();

               if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $imageFilename = str_pad($row['disease_id'], 5, '0', STR_PAD_LEFT) . "_1.jpg";
                    $imagePath = "http://localhost:8080/csP1015/project/img/D/" . $imageFilename;
                    $imageLocalPath = __DIR__ . "/../img/D/" . $imageFilename;
                    $imageAvailable = file_exists($imageLocalPath) ? $imagePath : "ไม่พบรูปภาพ";

                    $response = array(
                         "id" => $row['disease_id'],
                         "disease_name_th" => $row['disease_name_th'],
                         "disease_name_en" => $row['disease_name_en'],
                         "cause" => $row['cause'],
                         "symptoms" => $row['symptoms'],
                         "treatment" => $row['treatment'],
                         "prevention" => $row['prevention'],
                         "image" => $imageAvailable
                    );
               } else {
                    $response = array("message" => "ไม่พบข้อมูลโรคที่ระบุ");
               }
               echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
          } else {
               // คำนวณหน้าและการแสดงผล
               $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
               $limit = 5;
               $offset = ($page - 1) * $limit;

               $sql = "SELECT * FROM diseases LIMIT ? OFFSET ?";
               $stmt = $conn->prepare($sql);
               $stmt->bind_param("ii", $limit, $offset);
               $stmt->execute();
               $result = $stmt->get_result();

               $diseases = array();
               while ($row = $result->fetch_assoc()) {
                    $imageFilename = str_pad($row['disease_id'], 5, '0', STR_PAD_LEFT) . "_1.jpg";
                    $imagePath = "http://localhost:8080/csP1015/project/img/D/" . $imageFilename;
                    $imageLocalPath = __DIR__ . "/../img/D/" . $imageFilename;
                    $imageAvailable = file_exists($imageLocalPath) ? $imagePath : "ไม่พบรูปภาพ";

                    $diseases[] = array(
                         "id" => $row['disease_id'],
                         "disease_name_th" => $row['disease_name_th'],
                         "disease_name_en" => $row['disease_name_en'],
                         "image" => $imageAvailable
                    );
               }

               $sqlCount = "SELECT COUNT(*) AS total FROM diseases";
               $resultCount = $conn->query($sqlCount);
               $rowCount = $resultCount->fetch_assoc();
               $totalRows = $rowCount['total'];

               $totalPages = ceil($totalRows / $limit);

               $response["diseases"] = $diseases;
               $response["pagination"] = array(
                    "total_items" => $totalRows,
                    "total_pages" => $totalPages,
                    "current_page" => $page
               );
               echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
          }
          break;

     case 'POST':
          if ($_SERVER['REQUEST_METHOD'] === 'POST') {
               // Check if it's an update (ID exists) or new entry
               if (isset($_POST['id']) && !empty($_POST['id'])) {
                    // This is an update
                    $id = $_POST['id'];
                    $disease_name_th = $_POST['disease_name_th'];
                    $disease_name_en = $_POST['disease_name_en'];
                    $cause = $_POST['cause'];
                    $symptoms = $_POST['symptoms'];
                    $treatment = $_POST['treatment'];
                    $prevention = $_POST['prevention'];

                    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                         $imagePath = "../img/D/" . str_pad($id, 5, '0', STR_PAD_LEFT) . "_1.jpg";
                         move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
                    }

                    $sql = "UPDATE diseases SET disease_name_th = ?, disease_name_en = ?, cause = ?, symptoms = ?, treatment = ?, prevention = ? WHERE disease_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssssi", $disease_name_th, $disease_name_en, $cause, $symptoms, $treatment, $prevention, $id);

                    if ($stmt->execute()) {
                         header('Location: P1.php');
                         exit;
                    } else {
                         echo "<script>alert('เกิดข้อผิดพลาดในการแก้ไขข้อมูล');</script>";
                    }
               } else {
                    // This is a new entry
                    if (isset($_POST['disease_name_th'], $_POST['disease_name_en'], $_POST['cause'], $_POST['symptoms'], $_POST['treatment'], $_POST['prevention'])) {
                         $disease_name_th = $_POST['disease_name_th'];
                         $disease_name_en = $_POST['disease_name_en'];
                         $cause = $_POST['cause'];
                         $symptoms = $_POST['symptoms'];
                         $treatment = $_POST['treatment'];
                         $prevention = $_POST['prevention'];

                         // Insert new disease record
                         $sql = "INSERT INTO diseases (disease_name_th, disease_name_en, cause, symptoms, treatment, prevention) 
                                VALUES (?, ?, ?, ?, ?, ?)";
                         $stmt = $conn->prepare($sql);
                         $stmt->bind_param("ssssss", $disease_name_th, $disease_name_en, $cause, $symptoms, $treatment, $prevention);

                         if ($stmt->execute()) {
                              $new_id = $conn->insert_id;

                              // Handle image upload for new entry
                              if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                                   $imagePath = "../img/D/" . str_pad($new_id, 5, '0', STR_PAD_LEFT) . "_1.jpg";
                                   move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
                              }

                              header('Location: P1.php');
                              exit;
                         } else {
                              echo "<script>alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูล');</script>";
                         }
                    } else {
                         echo "<script>alert('ข้อมูลไม่ครบถ้วน');</script>";
                    }
               }
          }
          break;

     case 'PUT':
          $data = json_decode(file_get_contents("php://input"), true);
          if (isset($data['id'], $data['disease_name_th'], $data['disease_name_en'], $data['cause'], $data['symptoms'], $data['treatment'], $data['prevention'])) {
               $id = $data['id'];
               $disease_name_th = $data['disease_name_th'];
               $disease_name_en = $data['disease_name_en'];
               $cause = $data['cause'];
               $symptoms = $data['symptoms'];
               $treatment = $data['treatment'];
               $prevention = $data['prevention'];

               $sql = "UPDATE diseases SET disease_name_th = ?, disease_name_en = ?, cause = ?, symptoms = ?, treatment = ?, prevention = ? WHERE disease_id = ?";
               $stmt = $conn->prepare($sql);
               $stmt->bind_param("ssssssi", $disease_name_th, $disease_name_en, $cause, $symptoms, $treatment, $prevention, $id);

               if ($stmt->execute()) {
                    $response = array("message" => "แก้ไขข้อมูลโรคสำเร็จ");
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
               $json = file_get_contents("php://input");
               error_log("JSON ที่ได้รับ: " . $json);

               $data = json_decode($json, true);

               if (isset($data['id'])) {
                    $disease_id = intval($data['id']);
                    error_log("ID ที่จะลบ: " . $disease_id);

                    $sql = "DELETE FROM diseases WHERE disease_id = ?";
                    if ($stmt = $conn->prepare($sql)) {
                         $stmt->bind_param("i", $disease_id);
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
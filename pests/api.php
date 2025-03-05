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
               $pest_id = intval($_GET['id']);
               $sql = "SELECT * FROM pests WHERE id = ?";
               $stmt = $conn->prepare($sql);
               $stmt->bind_param("i", $pest_id);
               $stmt->execute();
               $result = $stmt->get_result();

               if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $imageFilename = str_pad($row['id'], 5, '0', STR_PAD_LEFT) . "_1.jpg";
                    $imagePath = "http://localhost:8080/csP1015/project/img/P/" . $imageFilename;
                    $imageLocalPath = __DIR__ . "/../img/P/" . $imageFilename;
                    $imageAvailable = file_exists($imageLocalPath) ? $imagePath : "ไม่พบรูปภาพ";

                    $response = array(
                         "id" => $row['id'],
                         "thai_name" => $row['name_th'],
                         "eng_name" => $row['name_en'],
                         "sci_name" => $row['name_sci'],
                         "appearance" => $row['appearance'],
                         "prevention" => $row['prevention'],
                         "details" => $row['details'],
                         "image" => $imageAvailable
                    );
               } else {
                    $response = array("message" => "ไม่พบข้อมูลศัตรูพืชที่ระบุ");
               }
               echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
          } else {
               $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
               $limit = 5;
               $offset = ($page - 1) * $limit;

               $sql = "SELECT * FROM pests LIMIT ? OFFSET ?";
               $stmt = $conn->prepare($sql);
               $stmt->bind_param("ii", $limit, $offset);
               $stmt->execute();
               $result = $stmt->get_result();

               $pests = array();
               while ($row = $result->fetch_assoc()) {
                    $imageFilename = str_pad($row['id'], 5, '0', STR_PAD_LEFT) . "_1.jpg";
                    $imagePath = "http://localhost:8080/csP1015/project/img/P/" . $imageFilename;
                    $imageLocalPath = __DIR__ . "/../img/P/" . $imageFilename;
                    $imageAvailable = file_exists($imageLocalPath) ? $imagePath : "ไม่พบรูปภาพ";

                    $pests[] = array(
                         "id" => $row['id'],
                         "thai_name" => $row['name_th'],
                         "eng_name" => $row['name_en'],
                         "image" => $imageAvailable
                    );
               }

               $sqlCount = "SELECT COUNT(*) AS total FROM pests";
               $resultCount = $conn->query($sqlCount);
               $rowCount = $resultCount->fetch_assoc();
               $totalRows = $rowCount['total'];
               $totalPages = ceil($totalRows / $limit);

               $response["pests"] = $pests;
               $response["pagination"] = array(
                    "total_items" => $totalRows,
                    "total_pages" => $totalPages,
                    "current_page" => $page
               );
               echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
          }
          break;

     case 'POST':
          if (isset($_POST['id'])) {
               // This is an update request
               $sql = "UPDATE pests SET name_th=?, name_en=?, name_sci=?, appearance=?, prevention=?, details=? WHERE id=?";
               $stmt = $conn->prepare($sql);
               $stmt->bind_param(
                    "ssssssi",
                    $_POST['name_th'],
                    $_POST['name_en'],
                    $_POST['name_sci'],
                    $_POST['appearance'],
                    $_POST['prevention'],
                    $_POST['details'],
                    $_POST['id']
               );

               if ($stmt->execute()) {
                    // Handle image update
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                         $pest_id = $_POST['id'];
                         $imagePath = "../img/P/" . str_pad($pest_id, 5, '0', STR_PAD_LEFT) . "_1.jpg";

                         // Delete existing image if it exists
                         if (file_exists($imagePath)) {
                              unlink($imagePath);
                         }

                         // Move new image
                         if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                              // Clear browser cache by adding timestamp to URL
                              header('Cache-Control: no-cache, no-store, must-revalidate');
                              header('Pragma: no-cache');
                              header('Expires: 0');
                              header('Location: PE1.php?t=' . time());
                              exit;
                         }
                    }

                    // If no new image was uploaded
                    header('Cache-Control: no-cache, no-store, must-revalidate');
                    header('Pragma: no-cache');
                    header('Expires: 0');
                    header('Location: PE1.php?t=' . time());
                    exit;
               } else {
                    $response = array("message" => "เกิดข้อผิดพลาดในการแก้ไขข้อมูล");
                    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
               }
          } else {
               // This is an insert request
               if (isset($_POST['name_th'], $_POST['name_en'], $_POST['name_sci'], $_POST['appearance'], $_POST['prevention'], $_POST['details'])) {
                    $sql = "INSERT INTO pests (name_th, name_en, name_sci, appearance, prevention, details) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param(
                         "ssssss",
                         $_POST['name_th'],
                         $_POST['name_en'],
                         $_POST['name_sci'],
                         $_POST['appearance'],
                         $_POST['prevention'],
                         $_POST['details']
                    );

                    if ($stmt->execute()) {
                         $new_id = $stmt->insert_id;
                         if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                              $imagePath = "../img/P/" . str_pad($new_id, 5, '0', STR_PAD_LEFT) . "_1.jpg";
                              move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
                         }
                         // Redirect to PE1.php after successful insertion
                         header('Location: PE1.php');
                         exit;
                    } else {
                         $response = array("message" => "เกิดข้อผิดพลาดในการเพิ่มข้อมูล");
                         echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    }
               }
          }
          break;

     case 'DELETE':
          $data = json_decode(file_get_contents("php://input"), true);
          if (isset($data['id'])) {
               $pest_id = intval($data['id']);
               $sql = "DELETE FROM pests WHERE id = ?";
               $stmt = $conn->prepare($sql);
               $stmt->bind_param("i", $pest_id);

               if ($stmt->execute()) {
                    $response = array("message" => "ลบข้อมูลสำเร็จ");
               } else {
                    $response = array("message" => "เกิดข้อผิดพลาดในการลบข้อมูล");
               }
               echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
          }
          break;
}

$conn->close();
?>
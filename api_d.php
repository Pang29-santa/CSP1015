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
                    $imageLocalPath = __DIR__ . "/img/D/" . $imageFilename;
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
               $limit = 10;
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
                    $imageLocalPath = __DIR__ . "/img/D/" . $imageFilename;
                    $imageAvailable = file_exists($imageLocalPath) ? $imagePath : "ไม่พบรูปภาพ";

                    $diseases[] = array(
                         "id" => $row['disease_id'],
                         "disease_name_th" => $row['disease_name_th'],
                         "disease_name_en" => $row['disease_name_en'],
                         "cause" => $row['cause'],
                         "symptoms" => $row['symptoms'],
                         "treatment" => $row['treatment'],
                         "prevention" => $row['prevention'],
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
}

$conn->close();
?>
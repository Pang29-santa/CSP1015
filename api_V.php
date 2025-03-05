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
                    $imageLocalPath = __DIR__ . "/img/V/" . $imageFilename;
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
               $limit = 10; // จำนวนรายการที่แสดงต่อหน้า
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
                    $imageLocalPath = __DIR__ . "/img/V/" . $imageFilename;
                    $imageAvailable = file_exists($imageLocalPath) ? $imagePath : "ไม่พบรูปภาพ";

                    $vegetables[] = array(
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
          break;}

$conn->close();
?>
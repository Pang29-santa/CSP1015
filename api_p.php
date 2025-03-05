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
               // ค้นหาข้อมูลศัตรูพืชตาม ID
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
                         "name_th" => $row['name_th'],
                         "name_en" => $row['name_en'],
                         "name_sci" => $row['name_sci'],
                         "appearance" => $row['appearance'],
                         "prevention" => $row['prevention'],
                         "details" => $row['details'],
                         "image" => $imageAvailable
                    );
               } else {
                    $response = array("message" => "ไม่พบข้อมูลศัตรูพืชที่ระบุ");
               }
          } else {
               $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
               $limit = 10;
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
                    $imageLocalPath = __DIR__ . "/img/P/" . $imageFilename;
                    $imageAvailable = file_exists($imageLocalPath) ? $imagePath : "ไม่พบรูปภาพ";

                    $pests[] = array(
                         "id" => $row['id'],
                         "name_th" => $row['name_th'],
                         "name_en" => $row['name_en'],
                         "name_sci" => $row['name_sci'],
                         "appearance" => $row['appearance'],
                         "prevention" => $row['prevention'],
                         "details" => $row['details'],
                         "image" => $imageAvailable
                    );
               }

               $sqlCount = "SELECT COUNT(*) AS total FROM pests";
               $resultCount = $conn->query($sqlCount);
               $rowCount = $resultCount->fetch_assoc();
               $totalRows = $rowCount['total'];
               $totalPages = ceil($totalRows / $limit);

               $response = array(
                    "pests" => $pests,
                    "pagination" => array(
                         "total_items" => $totalRows,
                         "total_pages" => $totalPages,
                         "current_page" => $page
                    )
               );
          }
          echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
          break;
}

$conn->close();
?>
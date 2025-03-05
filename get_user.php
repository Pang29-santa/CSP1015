<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include('db.php');

$response = array();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
     echo json_encode(array("status" => "error", "message" => "Unauthorized access, please log in."), JSON_UNESCAPED_UNICODE);
     exit;
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$sql = "SELECT user_id, username, full_name, email, phone_number FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_id, $username, $full_name, $email, $phone_number);
$stmt->fetch();
$stmt->close();

// ตรวจสอบว่ามีข้อมูลผู้ใช้หรือไม่
if (!$username) {
     echo json_encode(array("status" => "error", "message" => "User not found."), JSON_UNESCAPED_UNICODE);
     exit;
}

// สร้างชื่อไฟล์ภาพโปรไฟล์
$imageFilename = str_pad($user_id, 5, '0', STR_PAD_LEFT) . "_1.jpg";
$imagePath = "http://localhost:8080/csP1015/project/img/U/" . $imageFilename;
$imageLocalPath = __DIR__ . "img/U/" . $imageFilename;

// ตรวจสอบว่าไฟล์ภาพมีอยู่ในเซิร์ฟเวอร์หรือไม่
$imageAvailable = file_exists($imageLocalPath) ? $imagePath : "ไม่พบรูปภาพ";

// สร้างข้อมูลของผู้ใช้
$response = array(
     "status" => "success",
     "data" => array(
          "id" => $user_id,
          "username" => $username,
          "full_name" => $full_name,
          "email" => $email,
          "phone_number" => $phone_number,
          "profile_image" => $imageAvailable
     )
);

// ส่งข้อมูล JSON กลับไป
echo json_encode($response, JSON_UNESCAPED_UNICODE);

$conn->close();
?>
<?php
header('Content-Type: application/json');
session_start();
include '../db.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
     echo json_encode([
          'success' => false,
          'message' => 'Unauthorized access'
     ]);
     exit;
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้
$sql = "SELECT username, full_name, email, phone_number FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user) {
     // กำหนดเส้นทางรูปโปรไฟล์
     $imageFilename = str_pad($user_id, 5, '0', STR_PAD_LEFT) . "_1.jpg";
     $imagePath = "/csP1015/project/img/U/" . $imageFilename;
     $fullImagePath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;

     // ตรวจสอบว่ามีรูปโปรไฟล์หรือไม่
     $user['profile_image'] = file_exists($fullImagePath) ?
          'http://localhost:8080' . $imagePath . "?v=" . time() :
          'http://localhost:8080/csP1015/project/image/default_profile.jpg';

     echo json_encode([
          'success' => true,
          'data' => $user
     ]);
} else {
     echo json_encode([
          'success' => false,
          'message' => 'User not found'
     ]);
}

$conn->close();
?>
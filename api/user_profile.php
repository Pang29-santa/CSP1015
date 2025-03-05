<?php
session_start();
header('Content-Type: application/json');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['user_id'])) {
     http_response_code(401);
     echo json_encode(['success' => false, 'message' => 'Unauthorized']);
     exit;
}

include('../db.php');
$user_id = $_SESSION['user_id'];

try {
     $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
     $stmt->bind_param("i", $user_id);
     $stmt->execute();
     $result = $stmt->get_result();
     $user = $result->fetch_assoc();

     if (!$user) {
          throw new Exception('User not found');
     }

     // ตรวจสอบรูปภาพโปรไฟล์
     $imageFilename = str_pad($user_id, 5, '0', STR_PAD_LEFT) . "_1.jpg";
     $imagePath = "../img/U/" . $imageFilename;
     $imageUrl = file_exists($imagePath) ? "img/U/" . $imageFilename . "?v=" . time() : "img/default-profile.jpg";

     // Remove sensitive data
     unset($user['password']);
     $user['profile_image'] = $imageUrl;

     echo json_encode([
          'success' => true,
          'data' => $user
     ]);

} catch (Exception $e) {
     http_response_code(500);
     echo json_encode([
          'success' => false,
          'message' => $e->getMessage()
     ]);
}

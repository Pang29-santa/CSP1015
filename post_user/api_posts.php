<?php
header('Content-Type: application/json');
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
     echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
     exit;
}

$user_id = $_SESSION['user_id'];

// ดึงโพสต์ทั้งหมดที่เป็น active
$sql = "SELECT p.*, u.full_name, 
        (SELECT COUNT(*) FROM likes WHERE post_id = p.post_id) as likes_count,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.post_id) as comments_count,
        (SELECT COUNT(*) > 0 FROM likes WHERE post_id = p.post_id AND user_id = ?) as user_liked
        FROM posts p 
        JOIN users u ON p.user_id = u.user_id 
        WHERE p.status = 'active' 
        ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$posts = [];
while ($row = $result->fetch_assoc()) {
     $posts[] = $row;
}

echo json_encode([
     'success' => true,
     'data' => $posts
]);

$stmt->close();
$conn->close();
?>
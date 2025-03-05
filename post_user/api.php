<?php
header('Content-Type: application/json');
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

// เพิ่มส่วนจัดการ GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : null;

    if ($post_id) {
        // ดึงข้อมูลโพสต์เฉพาะ
        $sql = "SELECT p.*, u.full_name, 
                (SELECT COUNT(*) FROM likes WHERE post_id = p.post_id) as likes_count,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.post_id) as comments_count,
                EXISTS(SELECT 1 FROM likes WHERE post_id = p.post_id AND user_id = ?) as user_liked
                FROM posts p
                JOIN users u ON p.user_id = u.user_id
                WHERE p.post_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $_SESSION['user_id'], $post_id);
    } else {
        // ดึงข้อมูลโพสต์ทั้งหมด
        $sql = "SELECT p.*, u.full_name,
                (SELECT COUNT(*) FROM likes WHERE post_id = p.post_id) as likes_count,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.post_id) as comments_count,
                EXISTS(SELECT 1 FROM likes WHERE post_id = p.post_id AND user_id = ?) as user_liked
                FROM posts p
                JOIN users u ON p.user_id = u.user_id
                ORDER BY p.created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_SESSION['user_id']);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $posts = [];

    while ($row = $result->fetch_assoc()) {
        // แปลง boolean จาก MySQL เป็น PHP boolean
        $row['user_liked'] = (bool) $row['user_liked'];
        $row['created_at'] = date('Y-m-d H:i:s', strtotime($row['created_at']));
        $posts[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $post_id ? ($posts[0] ?? null) : $posts
    ]);

    $stmt->close();
    $conn->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $content = $_POST['post_content'] ?? '';

    if (empty($content)) {
        echo json_encode(['success' => false, 'message' => 'กรุณาใส่เนื้อหาโพสต์']);
        exit;
    }

    // เพิ่มลิงก์ถ้ามี
    if (isset($_POST['post_link']) && !empty($_POST['post_link'])) {
        $content .= "\n\nลิงก์: " . $_POST['post_link'];
    }

    try {
        $conn->begin_transaction();

        // สร้างโพสต์
        $sql = "INSERT INTO posts (user_id, content, created_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $content);
        $stmt->execute();
        $post_id = $conn->insert_id;

        // จัดการรูปภาพถ้ามี
        if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === 0) {
            $target_dir = dirname(__DIR__) . "/img/posts/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $filename = time() . '_' . basename($_FILES['post_image']['name']);
            $target_file = $target_dir . $filename;

            if (move_uploaded_file($_FILES['post_image']['tmp_name'], $target_file)) {
                $image_path = "/csP1015/project/img/posts/" . $filename;
                $update_sql = "UPDATE posts SET image_path = ? WHERE post_id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $image_path, $post_id);
                $update_stmt->execute();
            }
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'โพสต์สำเร็จ']);

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Create post error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการสร้างโพสต์']);
    }

    $conn->close();
    exit;
}

// สำหรับลบโพสต์
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $post_id = $data['post_id'];

    // ตรวจสอบเจ้าของโพสต์
    $check_sql = "SELECT user_id FROM posts WHERE post_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result && $result['user_id'] == $user_id) {
        $sql = "DELETE FROM posts WHERE post_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $post_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Delete failed']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    }
}

$conn->close();
?>
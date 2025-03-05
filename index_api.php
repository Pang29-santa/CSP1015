<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
     echo "Unauthorized access, please log in.";
     exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user information from the database
$sql = "SELECT username, full_name, email, phone_number FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($username, $full_name, $email, $phone_number);
$stmt->fetch();
$stmt->close();

// Check if user data is found
if (!$username) {
     echo "User not found.";
     exit;
}

// Set the profile image path
$imageFilename = str_pad($user_id, 5, '0', STR_PAD_LEFT) . "_1.jpg";
$imagePath = "http://localhost:8080/csP1015/project/img/U/" . $imageFilename; // Update with the correct URL
$imageLocalPath = __DIR__ . "/img/U/" . $imageFilename;

// Check if the profile image exists
$imageAvailable = file_exists($imageLocalPath) ? $imagePath : "ไม่พบรูปภาพ";

// Debugging line to check the image path
// echo "Image Path: " . $imageAvailable; // This will output the image path to check
?>
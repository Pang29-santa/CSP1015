<?php
session_start();

if (!isset($_SESSION['user_id'])) {
     header("Location: login.php");
     exit;
}

include('db.php');
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$imageFilename = str_pad($user_id, 5, '0', STR_PAD_LEFT) . "_1.jpg";
$imagePath = "./img/U/" . $imageFilename;
$imageUrl = file_exists($imagePath) ? $imagePath . "?v=" . time() : "img/default-profile.jpg";
?>

<!DOCTYPE html>
<html lang="th">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>แก้ไขโปรไฟล์</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
     <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600&display=swap" rel="stylesheet">
     <style>
          /* Copy the styles from the original profile.php */
     </style>
</head>

<body>
     <div class="container profile-section">
          <div class="card shadow">
               <div class="card-header">
                    <h3 class="text-center mb-0 text-white">แก้ไขข้อมูลโปรไฟล์</h3>
               </div>
               <div class="card-body p-4">
                    <!-- Copy the form section from the original profile.php -->
     
               </div>
          </div>
     </div>

     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
     <script>
          // Copy the JavaScript from the original profile.php
     </script>
</body>

</html>
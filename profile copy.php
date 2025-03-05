<?php
session_start();

if (!isset($_SESSION['user_id'])) {
     header("Location: login.php");
     exit;
}

// เพิ่มการป้องกันการแคช
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// ดึงข้อมูลผู้ใช้จากฐานข้อมูลโดยตรง
include('db.php');
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
     header("Location: login.php");
     exit;
}

// ตรวจสอบรูปภาพโปรไฟล์
$imageFilename = str_pad($user_id, 5, '0', STR_PAD_LEFT) . "_1.jpg";
$imagePath = "./img/U/" . $imageFilename;
// เพิ่มตัวแปรเวลาเพื่อป้องกันการแคช
$imageUrl = file_exists($imagePath) ? $imagePath . "?v=" . time() : "img/default-profile.jpg";
?>

<!DOCTYPE html>
<html lang="th">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>โปรไฟล์ของฉัน</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
     <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600&display=swap" rel="stylesheet">
     <style>
          body {
               font-family: 'Kanit', sans-serif;
               background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
               min-height: 100vh;
               padding: 40px 0;
          }

          .profile-section {
               max-width: 900px;
               margin: 2rem auto;
          }

          .card {
               border: none;
               border-radius: 15px;
               box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
          }

          .card-header {
               background: linear-gradient(135deg, #45B649 0%, #096b32 100%);
               border-radius: 15px 15px 0 0 !important;
               padding: 1.5rem;
          }

          .profile-image {
               width: 180px;
               height: 180px;
               object-fit: cover;
               border-radius: 50%;
               border: 4px solid #fff;
               box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
               transition: transform 0.3s ease;
          }

          .profile-image:hover {
               transform: scale(1.05);
          }

          .form-control {
               border-radius: 8px;
               padding: 12px 15px;
               border: 1px solid #ddd;
               transition: all 0.3s ease;
          }

          .form-control:focus {
               box-shadow: 0 0 0 3px rgba(69, 182, 73, 0.2);
               border-color: #45B649;
          }

          .form-label {
               font-weight: 500;
               color: #2c3e50;
               margin-bottom: 8px;
               font-size: 0.95rem;
          }

          .password-section {
               background-color: #f8f9fa;
               border-radius: 12px;
               padding: 25px !important;
               border: 1px solid #e9ecef !important;
          }

          .btn {
               padding: 12px 30px;
               border-radius: 8px;
               font-weight: 500;
               transition: all 0.3s ease;
               margin: 5px;
          }

          .btn-primary {
               background: #45B649;
               border: none;
               box-shadow: 0 4px 15px rgba(69, 182, 73, 0.3);
          }

          .btn-primary:hover {
               background: #3a9b3e;
               transform: translateY(-2px);
          }

          .btn-secondary {
               background: #6c757d;
               border: none;
               box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
          }

          .btn-secondary:hover {
               background: #5a6268;
               transform: translateY(-2px);
          }

          .alert {
               border-radius: 10px;
               border: none;
          }

          .custom-file-upload {
               display: inline-block;
               padding: 10px 20px;
               background: #45B649;
               color: white;
               border-radius: 25px;
               cursor: pointer;
               transition: all 0.3s ease;
               margin-top: 15px;
          }

          .custom-file-upload:hover {
               background: #3a9b3e;
               transform: translateY(-2px);
          }

          input[type="file"] {
               display: none;
          }

          h5 {
               color: #2c3e50;
               font-weight: 500;
               margin-bottom: 20px;
          }

          .password-input-group {
               position: relative;
          }

          .password-toggle {
               position: absolute;
               right: 10px;
               top: 50%;
               transform: translateY(-50%);
               border: none;
               background: none;
               cursor: pointer;
               color: #6c757d;
               padding: 5px;
               z-index: 10;
          }

          .password-toggle:hover {
               color: #45B649;
          }

          .password-input-group .form-control {
               padding-right: 40px;
          }
     </style>
</head>

<body class="bg-light">
     <div class="container profile-section">
          <div class="card shadow">
               <div class="card-header">
                    <h3 class="text-center mb-0 text-white">ข้อมูลโปรไฟล์</h3>
                    <p class="text-center mb-0 mt-2 text-white-50">จัดการข้อมูลส่วนตัวของคุณ</p>
               </div>
               <div class="card-body p-4">
                    <?php if (isset($_SESSION['message'])): ?>
                         <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                              <?php
                              echo $_SESSION['message'];
                              unset($_SESSION['message']);
                              unset($_SESSION['message_type']);
                              ?>
                              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                         </div>
                    <?php endif; ?>

                    <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                         <div class="text-center mb-4">
                              <img src="<?php echo htmlspecialchars($imageUrl); ?>" class="profile-image mb-3"
                                   alt="รูปโปรไฟล์">
                              <div>
                                   <label for="profile_image" class="custom-file-upload">
                                        <i class="bi bi-camera-fill me-2"></i>เปลี่ยนรูปโปรไฟล์
                                   </label>
                                   <input id="profile_image" type="file" name="profile_image" accept="image/*">
                              </div>
                         </div>

                         <div class="row g-4">
                              <div class="col-md-6">
                                   <label class="form-label"><i class="bi bi-person me-2"></i>ชื่อผู้ใช้</label>
                                   <input type="text" name="username" class="form-control bg-light"
                                        value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                              </div>
                              <div class="col-md-6">
                                   <label class="form-label"><i class="bi bi-envelope me-2"></i>อีเมล</label>
                                   <input type="email" name="email" class="form-control"
                                        value="<?php echo htmlspecialchars($user['email']); ?>" required>
                              </div>
                              <div class="col-md-6">
                                   <label class="form-label"><i class="bi bi-person-badge me-2"></i>ชื่อ-นามสกุล</label>
                                   <input type="text" name="full_name" class="form-control"
                                        value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                              </div>
                              <div class="col-md-6">
                                   <label class="form-label"><i class="bi bi-telephone me-2"></i>เบอร์โทรศัพท์</label>
                                   <input type="tel" name="phone_number" class="form-control"
                                        value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                              </div>
                         </div>

                         <div class="password-section mt-4">
                              <h5><i class="bi bi-shield-lock me-2"></i>เปลี่ยนรหัสผ่าน</h5>
                              <div class="mb-3">
                                   <label class="form-label">รหัสผ่านปัจจุบัน</label>
                                   <div class="password-input-group">
                                        <input type="password" name="current_password" class="form-control"
                                             id="currentPassword">
                                        <button type="button" class="password-toggle"
                                             onclick="togglePassword('currentPassword')">
                                             <i class="bi bi-eye-slash"></i>
                                        </button>
                                   </div>
                              </div>
                              <div class="mb-3">
                                   <label class="form-label">รหัสผ่านใหม่</label>
                                   <div class="password-input-group">
                                        <input type="password" name="new_password" class="form-control"
                                             id="newPassword">
                                        <button type="button" class="password-toggle"
                                             onclick="togglePassword('newPassword')">
                                             <i class="bi bi-eye-slash"></i>
                                        </button>
                                   </div>
                              </div>
                              <div class="mb-3">
                                   <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                                   <div class="password-input-group">
                                        <input type="password" name="confirm_password" class="form-control"
                                             id="confirmPassword">
                                        <button type="button" class="password-toggle"
                                             onclick="togglePassword('confirmPassword')">
                                             <i class="bi bi-eye-slash"></i>
                                        </button>
                                   </div>
                              </div>
                         </div>

                         <div class="text-center mt-4">
                              <button type="submit" class="btn btn-primary">
                                   <i class="bi bi-save me-2"></i>บันทึกการเปลี่ยนแปลง
                              </button>
                              <a href="index.php" class="btn btn-secondary">
                                   <i class="bi bi-arrow-left me-2"></i>กลับหน้าหลัก
                              </a>
                         </div>
                    </form>
               </div>
          </div>
     </div>

     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
     <script>
          // Preview image before upload
          document.getElementById('profile_image').addEventListener('change', function (e) {
               if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                         document.querySelector('.profile-image').src = e.target.result;
                    }
                    reader.readAsDataURL(e.target.files[0]);
               }
          });

          // Add password visibility toggle function
          function togglePassword(inputId) {
               const input = document.getElementById(inputId);
               const icon = event.currentTarget.querySelector('i');

               if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
               } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
               }
          }
     </script>
</body>

</html>
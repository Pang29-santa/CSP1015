<?php
session_start();

if (!isset($_SESSION['user_id'])) {
     echo "Unauthorized access, please log in.";
     exit;
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="th">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>เว็บไซต์โรคพืชในผักสวนครัว</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="styles.css">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
     <link href="https://fonts.googleapis.com/css?family=Kanit&subset=thai,latin" rel="stylesheet" type="text/css">
     <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
     <style>
          .user-name {
               font-size: 20px;
               margin-left: 10px;
               vertical-align: middle;
               display: inline-block;
               max-width: 150px;
               white-space: nowrap;
               overflow: hidden;
               text-overflow: ellipsis;
          }
     </style>
</head>

<body>
     <div class="nav-container">
          <nav class="navbar">
               <div class="logo">
                    <img src="image/vegetables.png" alt="Logo"> <!-- ใส่โลโก้ของคุณที่นี่ -->
               </div>
               <ul class="nav-links">
                    <li><a href="index.php">หน้าหลัก</a></li>
                    <li><a href="vegetable.php">ข้อมูลพืชผักสวนครัว</a></li>
                    <li><a href="diseases.php">ข้อมูลโรคพืช</a></li>
                    <li><a href="Pest.php">ข้อมูลแมลงศัตรูพืช</a></li>
                    <li><a href="./post_user/Po1.php">แชร์กัน</a></li>
               </ul>
               <div class="user-profile">
                    <li class="nav-item dropdown">
                         <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                              data-bs-toggle="dropdown" aria-expanded="false">
                              <img src="<?php echo $_SESSION['profile_image'] ?? 'default-profile.jpg'; ?>"
                                   alt="Profile Image" class="rounded-circle" width="60px" height="60px">
                              <span class="user-name"><?php echo $_SESSION['full_name'] ?? 'Guest'; ?></span>
                         </a>
                         <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                              <li><a class="dropdown-item" href="profile.php">โปรไฟล์</a></li>
                              <li><a class="dropdown-item" href="index.html"
                                        onclick="return confirm('คุณต้องการออกจากระบบหรือไม่?');">ออกจากระบบ</a></li>
                         </ul>
                    </li>
               </div>
          </nav>
     </div>
     <!-- Ultrasonic Wave Information Section -->
     <section class="wave-section">

          <!-- Section: โรคของผัก -->
          <div class="wave-container">
               <div class="wave-header">
                    <h2>โรคพืช</h2>
                    <p>โรคของผักเป็นปัญหาที่พบได้บ่อยในการเกษตร ซึ่งสามารถส่งผลต่อการเจริญเติบโตและผลผลิตของพืช
                         หากไม่ได้รับการดูแลรักษาอย่างเหมาะสม</p>
               </div>
               <div class="feature-grid">
                    <div class="feature-card">
                         <div class="feature-icon">
                              <i class="bi bi-bug"></i>
                         </div>
                         <div class="feature-content">
                              <h3>โรคใบจุด (Leaf Spot)</h3>
                              <p>เกิดจากเชื้อราหรือแบคทีเรียที่ทำลายใบพืช ทำให้เกิดจุดเหลืองหรือดำบนใบ
                                   ส่งผลให้ใบร่วงและลดประสิทธิภาพการ photosynthesis</p>
                         </div>
                    </div>
                    <div class="feature-card">
                         <div class="feature-icon">
                              <i class="bi bi-droplet"></i>
                         </div>
                         <div class="feature-content">
                              <h3>โรคโคนเน่า (Root Rot)</h3>
                              <p>เกิดจากเชื้อราที่เจริญเติบโตในดินและทำลายรากพืช
                                   ทำให้พืชไม่สามารถดูดน้ำและสารอาหารได้อย่างเพียงพอ</p>
                         </div>
                    </div>
                    <div class="feature-card">
                         <div class="feature-icon">
                              <i class="bi bi-cloud-sun"></i>
                         </div>
                         <div class="feature-content">
                              <h3>โรคแอนแทรคโนส (Anthracnose)</h3>
                              <p>เกิดจากเชื้อราที่ทำลายเนื้อเยื่อของพืชและทำให้เกิดแผลที่เปลี่ยนสีบนใบและผล</p>
                         </div>
                    </div>
                    <div class="feature-card">
                         <div class="feature-icon">
                              <i class="bi bi-shield-lock"></i>
                         </div>
                         <div class="feature-content">
                              <h3>โรคเหี่ยว (Wilt)</h3>
                              <p>เกิดจากเชื้อราหรือแบคทีเรียที่เข้าไปในเส้นเลือดของพืช
                                   ทำให้พืชไม่สามารถรับน้ำได้อย่างเหมาะสมและทำให้เหี่ยว</p>
                         </div>
                    </div>
               </div>
               <div class="wave-details">
                    <h3>สาเหตุของโรคผัก</h3>
                    <ul>
                         <li>เชื้อราหรือแบคทีเรียที่ติดเชื้อผ่านทางดินหรืออากาศ</li>
                         <li>สภาพอากาศที่ไม่เหมาะสม เช่น ความชื้นสูงหรืออุณหภูมิที่ไม่เหมาะสม</li>
                         <li>การปลูกพืชในพื้นที่ที่ไม่สะอาดหรือมีการปนเปื้อนของโรคจากพืชอื่น</li>
                         <li>การใช้สารเคมีที่ไม่เหมาะสมที่อาจทำให้พืชอ่อนแอและติดเชื้อได้ง่ายขึ้น</li>
                    </ul>
                    <div class="info-box">
                         <p>💡 ทิป: ควรตรวจสอบและบำรุงรักษาสภาพแวดล้อมการปลูกผักให้เหมาะสม เช่น
                              การระบายน้ำและการควบคุมความชื้น เพื่อป้องกันการเกิดโรค</p>
                    </div>
               </div>
          </div>
     </section>
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
     <script>
          $(document).ready(function () {
               loadUserData();
          });

          function loadUserData() {
               $.ajax({
                    url: 'http://localhost:8080/csP1015/project/api_user.php',
                    method: 'GET',
                    success: function (response) {
                         if (response.success) {
                              const user = response.data;
                              updateUserNavigation(user);
                         }
                    }
               });
          }

          function updateUserNavigation(user) {
               // แยกการอัพเดตรูปภาพและชื่อ
               $('#navbarDropdown img').attr('src', user.profile_image);
               // อัพเดตชื่อและเพิ่ม title สำหรับกรณีชื่อยาวเกินไป
               $('#navbarDropdown span.user-name').text(user.full_name)
                    .attr('title', user.full_name);
          }
     </script>
</body>

</html>
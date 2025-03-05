<?php
session_start();

if (!isset($_SESSION['user_id'])) {
     echo "Unauthorized access, please log in.";
     exit;
}

$user_id = $_SESSION['user_id'];
?>
<?php
// ดึงข้อมูลจาก API
$apiUrl = "http://localhost:8080/csP1015/project/api_V.php?page=" . ($_GET['page'] ?? 1);
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

// ตรวจสอบว่ามีข้อมูลหรือไม่
$vegetables = $data['vegetables'] ?? [];
$totalPages = $data['pagination']['total_pages'] ?? 1;
$page = $_GET['page'] ?? 1;
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
          textarea {
               margin-top: 15px;
               margin-bottom: 20px;
               /* ปรับระยะห่างด้านล่าง */
          }

          p {
               margin-top: 15px;
               /* ปรับระยะห่างด้านบน */
          }

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
                    <li><a href="#">ข้อมูลพืชผักสวนครัว</a></li>
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
                              <span class="user-name"><?php echo $_SESSION['full_name'] ?? ''; ?></span>
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
          <div class="wave-container">
               <div class="wave-header">
                    <h2>ข้อมูลพืชผักสวนครัว</h2>
               </div>
               <div class="row">
                    <?php foreach ($vegetables as $index => $row): ?>
                         <div class="col-md-4 mb-4"> <!-- Bootstrap col-md-4 creates 3 columns in a row -->
                              <div class="feature-card" data-bs-toggle="modal" data-bs-target="#modal-<?php echo $index; ?>">
                                   <?php
                                   $vegetable_id = str_pad($row['vegetable_id'] ?? 0, 5, '0', STR_PAD_LEFT);
                                   $imagePath = htmlspecialchars($row['image']);
                                   // เพิ่มพารามิเตอร์เวลาเพื่อป้องกันการแคช
                                   $imagePath .= (strpos($imagePath, '?') === false ? '?' : '&') . "v=" . time();
                                   ?>
                                   <img src="<?php echo $imagePath; ?>"
                                        alt="<?php echo htmlspecialchars($row['thai_name']); ?>" class="img-thumbnail"
                                        style="width:250px; height: 220px;">
                                   <div class="feature-content">
                                        <h3><?php echo htmlspecialchars($row['thai_name']); ?></h3>
                                        <p><?php echo htmlspecialchars($row['eng_name']); ?></p>
                                   </div>
                              </div>

                              <!-- Modal -->
                              <div class="modal fade" id="modal-<?php echo $index; ?>" tabindex="-1"
                                   aria-labelledby="modalLabel-<?php echo $index; ?>" aria-hidden="true">
                                   <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                             <div class="modal-header">
                                                  <h5 class="modal-title" id="modalLabel-<?php echo $index; ?>">
                                                       <?php echo htmlspecialchars($row['thai_name']); ?>
                                                  </h5>
                                                  <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                       aria-label="Close"></button>
                                             </div>
                                             <div class="modal-body text-center">
                                                  <img src="<?php echo $imagePath; ?>"
                                                       alt="<?php echo htmlspecialchars($row['thai_name']); ?>"
                                                       class="img-fluid mb-3" style="width: 550px;">
                                                  <p class="text-start"><b>ชื่อภาษาไทย :</b>
                                                       <?php echo htmlspecialchars($row['thai_name']); ?>
                                                  </p>
                                                  <p class="text-start"><b>ชื่อภาษาอังกฤษ :</b>
                                                       <?php echo htmlspecialchars($row['eng_name']); ?>
                                                  </p>
                                                  <p class="text-start"><b>ชื่อวิทยาศาสตร์ :</b>
                                                       <?php echo htmlspecialchars($row['sci_name']); ?>
                                                  </p>
                                                  <p class="text-start"><b>ระยะเวลาปลูกจนเก็บเกี่ยว :</b>
                                                       <?php echo htmlspecialchars($row['growth']); ?> วัน
                                                  </p>
                                                  <label for="planting_method"
                                                       class="form-label text-start d-block"><b>วิธีปลูก</b></label>
                                                  <textarea class="form-control border-0 shadow-none text-start p-0"
                                                       name="planting_method" rows="4" required><?php echo htmlspecialchars($row['planting_method']); ?>
                                                                                          </textarea>
                                                  <label for="planting_method"
                                                       class="form-label text-start d-block"><b>การดูแล</b></label>
                                                  <textarea class="form-control border-0 shadow-none text-start p-0"
                                                       name="care" rows="5" required><?php echo htmlspecialchars($row['care']); ?>
                                                                                          </textarea>
                                                  <label for="planting_method"
                                                       class="form-label text-start d-block"><b>รายละเอียด</b></label>
                                                  <textarea class="form-control border-0 shadow-none text-start p-0"
                                                       name="details" rows="4" required><?php echo htmlspecialchars($row['details']); ?>
                                                                                          </textarea>
                                             </div>
                                             <div class="modal-footer">
                                                  <button type="button" class="btn btn-secondary"
                                                       data-bs-dismiss="modal">ปิด</button>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    <?php endforeach; ?>
               </div>
          </div>
     </section>

     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
     <!-- Bootstrap Bundle รวม Popper.js ด้วย -->
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
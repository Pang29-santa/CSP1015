<?php
// ดึงข้อมูลจาก API
$apiUrl = "http://localhost:8080/csP1015/project/users/api.php?id=" . ($_GET['id'] ?? '');
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

// ตรวจสอบว่ามีข้อมูลหรือไม่
if (!isset($data['id'])) {
     die("<h2 class='text-center text-danger'>ไม่พบข้อมูลผู้ใช้ที่ระบุ</h2>");
}
?>


<!DOCTYPE html>
<html lang="th">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>ระบบจัดการข้อมูลผู้ใช้</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
     <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
     <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
     <link rel="icon" href="img/user.png" type="image/png">
     <link rel="stylesheet" href="../css/style_v.css">
     <script>
          // การแสดงข้อความยืนยัน
          document.addEventListener('DOMContentLoaded', function () {
               const form = document.getElementById('userForm');
               form.addEventListener('submit', function (event) {
                    event.preventDefault();  // ป้องกันการส่งฟอร์มทันที
                    if (confirm("คุณต้องการบันทึกข้อมูลหรือไม่?")) {
                         form.submit();  // ส่งฟอร์มหลังจากยืนยัน
                    }
               });
          });
     </script>
     <style>
          input[type="text"],
          input[type="email"],
          input[type="password"] {
               width: 100%;
               /* ขยายให้เต็มความกว้าง */
               height: 40px;
               /* กำหนดความสูงให้เหมาะสม */
          }
     </style>
</head>

<body>
     <div class="container-fluid">
          <div class="row">
               <!-- Sidebar -->
               <div class="col-md-2 sidebar p-0">
                    <div class="p-4 text-center border-bottom">
                         <h5 class="text-white">Vegetables</h5>
                    </div>
                    <nav class="nav flex-column p-2">
                         <a class="nav-link" href="../vegetable/V1.php">
                              <i class="bi bi-tree"></i> ข้อมูลผัก
                         </a>
                         <a class="nav-link" href="../plant_diseases/P1.php">
                              <i class="bi bi-bug"></i> ข้อมูลโรคพืช
                         </a>
                         <a class="nav-link" href="../pests/PE1.php"><i class="bi-bug-fill"></i> ข้อมูลแมลงศัตรูพืช</a>
                         <a class="nav-link" href="../post_admin/post.php"><i class="bi bi-newspaper"></i> ข้อมูลโพส</a>
                         <a class="nav-link active" href="U1.php">
                              <i class="bi-person-circle"></i> ข้อมูลผู้ใช้งาน
                         </a>
                    </nav>
               </div>

               <!-- Main Content -->
               <div class="col-md-10 p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                         <h2>แก้ไขข้อมูลผู้ใช้</h2>
                         <div class="col-auto">
                              <div class="dropdown">
                                   <button class="btn btn-transparent dropdown-toggle" type="button" id="userDropdown"
                                        data-bs-toggle="dropdown">
                                        <img src="../image/cat.png" alt="Profile" class="rounded-circle me-2"
                                             style="width: 30px; height: 30px; object-fit: cover;">Admin
                                   </button>
                                   <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                        <li>
                                             <div class="d-flex align-items-center px-3 py-2 border-bottom">
                                                  <img src="../image/cat.png" alt="Profile" class="rounded-circle me-3"
                                                       style="width: 50px; height: 50px; object-fit: cover;">
                                                  <div>
                                                       <h6 class="m-0">Admin</h6>
                                                       <small class="text-muted">admin@example.com</small>
                                                  </div>
                                             </div>
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="#"><i
                                                       class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</a>
                                        </li>
                                   </ul>
                              </div>
                         </div>
                    </div>

                    <!-- Form -->
                    <div class="table-wrapper">
                         <div class="main-content p-4">
                              <form id="userForm" action="http://localhost:8080/csP1015/project/users/api.php"
                                   method="POST" enctype="multipart/form-data">
                                   <input type="hidden" name="id" value="<?php echo htmlspecialchars($data['id']); ?>">
                                   <div class="mb-3">
                                        <label for="username" class="form-label">ชื่อผู้ใช้</label>
                                        <input type="text" class="form-control" id="username" name="username"
                                             value="<?php echo htmlspecialchars($data['username']); ?>" required>
                                   </div>
                                   <div class="mb-3">
                                        <label for="full_name" class="form-label">ชื่อ-นามสกุล</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name"
                                             value="<?php echo htmlspecialchars($data['full_name']); ?>" required>
                                   </div>
                                   <div class="mb-3">
                                        <label for="email" class="form-label">อีเมล</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                             value="<?php echo htmlspecialchars($data['email']); ?>" required>
                                   </div>
                                   <div class="mb-3">
                                        <label for="phone_number" class="form-label">เบอร์โทรศัพท์</label>
                                        <input type="text" class="form-control" id="phone_number" name="phone_number"
                                             value="<?php echo htmlspecialchars($data['phone_number']); ?>" required>
                                   </div>
                                   <div class="mb-3">
                                        <label for="password" class="form-label">รหัสผ่าน
                                             (ปล่อยว่างไว้หากไม่ต้องการเปลี่ยน)</label>
                                        <input type="password" class="form-control" id="password" name="password">
                                   </div>
                                   <div class="mb-3">
                                        <label for="confirm_password" class="form-label">ยืนยันรหัสผ่าน</label>
                                        <input type="password" class="form-control" id="confirm_password"
                                             name="confirm_password">
                                   </div>

                                   <div class="mb-3">
                                        <label for="image" class="form-label">เลือกรูปโปรไฟล์</label>
                                        <input type="file" class="form-control mb-3" id="image" name="image"
                                             accept="image/*">
                                        <?php if (isset($data['image']) && $data['image'] !== 'ไม่พบรูปภาพ'): ?>
                                             <img src="<?php echo htmlspecialchars($data['image']); ?>?v=<?php echo time(); ?>"
                                                  class="img-fluid" alt="รูปโปรไฟล์"
                                                  style="max-width: 300px; border-radius: 10px;">
                                        <?php else: ?>
                                             <div class="alert alert-secondary">ยังไม่มีรูปโปรไฟล์</div>
                                        <?php endif; ?>
                                   </div>

                                   <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                                   <a href="U1.php" class="btn btn-secondary ms-2">ยกเลิก</a>
                              </form>
                         </div>
                    </div>
               </div>
          </div>
     </div>
     <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
     <script>
          // ตรวจสอบรหัสผ่านที่ตรงกัน
          document.getElementById('userForm').addEventListener('submit', function (event) {
               const password = document.getElementById('password').value;
               const confirmPassword = document.getElementById('confirm_password').value;

               if (password !== '' && password !== confirmPassword) {
                    event.preventDefault();
                    alert('รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน กรุณาตรวจสอบอีกครั้ง');
               }
          });
     </script>
</body>

</html>
<?php
$apiUrl = "http://localhost:8080/csP1015/project/pests/api.php?id=" . ($_GET['id'] ?? '');
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

// ตรวจสอบว่ามีข้อมูลหรือไม่
if (!isset($data['id'])) {
     die("<h2 class='text-center text-danger'>ไม่พบข้อมูลผักที่ระบุ</h2>");
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>ระบบตรวจสอบพืชสวนครัว</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
     <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
     <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
     <link rel="icon" href="img/plant.png" type="image/png">
     <link rel="stylesheet" href="../css/style_v_add.css">
     <script>
          // การแสดงข้อความยืนยัน
          document.addEventListener('DOMContentLoaded', function () {
               const form = document.getElementById('vegForm');
               form.addEventListener('submit', function (event) {
                    event.preventDefault();  // ป้องกันการส่งฟอร์มทันที
                    if (confirm("คุณต้องการบันทึกข้อมูลหรือไม่?")) {
                         form.submit();  // ส่งฟอร์มหลังจากยืนยัน
                    }
               });
          });
     </script>
     <style>
          input[type="text"] {
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
                         <a class="nav-link active" href="PE1.php"><i class="bi-bug-fill"></i> ข้อมูลแมลงศัตรูพืช</a>
                         <a class="nav-link" href="../post_admin/post.php"><i class="bi-newspaper"></i> ข้อมูลโพส</a>
                         <a class="nav-link" href="../Users/U1.php"><i class="bi-person-circle"></i> ข้อมูลผู้ใช้งาน</a>

                    </nav>
               </div>

               <!-- Main Content -->
               <div class="col-md-10 p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                         <h2>รายละเอียดแมลงศัตรูพืช</h2>
                         <div class="dropdown">
                              <button class="btn btn-light dropdown-toggle d-flex align-items-center" type="button"
                                   data-bs-toggle="dropdown">
                                   <img src="../image/cat.png" alt="Profile" class="rounded-circle me-2"
                                        style="width: 30px; height: 30px; object-fit: cover;">
                                   <span>Admin</span>
                              </button>
                              <ul class="dropdown-menu dropdown-menu-end">
                                   <li class="px-3 py-2 d-flex align-items-center border-bottom">
                                        <img src="../image/cat.png" alt="Profile" class="rounded-circle me-2"
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                             <div class="fw-bold">Admin</div>
                                             <small class="text-muted">admin@example.com</small>
                                        </div>
                                   </li>
                                   <li><a class="dropdown-item text-danger" href="#"><i
                                                  class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</a></li>
                              </ul>
                         </div>
                    </div>

                    <!-- Detail Section -->
                    <div class="table-wrapper">
                         <div class="main-content p-4">
                              <div class="mb-3">
                                   <label class="form-label">ชื่อภาษาไทย</label>
                                   <input type="text" class="form-control"
                                        value="<?php echo htmlspecialchars($data['thai_name']); ?>" readonly>
                              </div>
                              <div class="mb-3">
                                   <label class="form-label">ชื่อภาษาอังกฤษ</label>
                                   <input type="text" class="form-control"
                                        value="<?php echo htmlspecialchars($data['eng_name']); ?>" readonly>
                              </div>
                              <div class="mb-3">
                                   <label class="form-label">ชื่อวิทยาศาสตร์</label>
                                   <input type="text" class="form-control"
                                        value="<?php echo htmlspecialchars($data['sci_name']); ?>" readonly>
                              </div>
                              <div class="mb-3">
                                   <label class="form-label">ลักษณะ</label>
                                   <textarea class="form-control"
                                        readonly><?php echo htmlspecialchars($data['appearance']); ?></textarea>
                              </div>
                              <div class="mb-3">
                                   <label class="form-label">การป้องกัน</label>
                                   <textarea class="form-control"
                                        readonly><?php echo htmlspecialchars($data['prevention']); ?></textarea>
                              </div>
                              <div class="mb-3">
                                   <label class="form-label">รายละเอียด</label>
                                   <textarea class="form-control"
                                        readonly><?php echo htmlspecialchars($data['details']); ?></textarea>
                              </div>
                              <div class="mb-3">
                                   <img src="<?php echo htmlspecialchars($data['image']); ?>" class="img-fluid"
                                        alt="รูปภาพแมลง" style="width:300px;">
                              </div>
                              <a href="PE1.php">
                                   <button type="submit" class="btn btn-primary">กลับ</button>
                              </a>
                         </div>
                    </div>
               </div>

          </div>
     </div>
     <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>

<?php $stmt->close(); ?>
<?php $conn->close(); ?>
<?php
// ดึงข้อมูลจาก API
$apiUrl = "http://localhost:8080/csP1015/project/plant_diseases/api.php?id=" . ($_GET['id'] ?? '');
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

// ตรวจสอบว่ามีข้อมูลหรือไม่
if (!isset($data['id'])) {
     die("<h2 class='text-center text-danger'>ไม่พบข้อมูลโรคพืชที่ระบุ</h2>");
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>ระบบตรวจสอบโรคพืช</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
     <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
     <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
     <link rel="icon" href="img/plant.png" type="image/png">
     <link rel="stylesheet" href="../css/style_v_add.css">
     <script>
          document.addEventListener('DOMContentLoaded', function () {
               const form = document.getElementById('diseaseForm');
               form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    if (confirm("คุณต้องการบันทึกข้อมูลหรือไม่?")) {
                         form.submit();
                    }
               });
          });
     </script>
     <style>
          input[type="text"] {
               width: 100%;
               height: 40px;
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
                         <a class="nav-link" href="../vegetable/V1.php"><i class="bi bi-tree"></i> ข้อมูลผัก</a>
                         <a class="nav-link active" href="#"><i class="bi bi-bug"></i> ข้อมูลโรคพืช</a>
                         <a class="nav-link" href="../pests/PE1.php"><i class="bi-bug-fill"></i> ข้อมูลแมลงศัตรูพืช</a>
                         <a class="nav-link" href="../post_admin/post.php"><i class="bi-newspaper"></i> ข้อมูลโพส</a>
                         <a class="nav-link" href="../Users/U1.php"><i class="bi-person-circle"></i> ข้อมูลผู้ใช้งาน</a>
                    </nav>
               </div>

               <!-- Main Content -->
               <div class="col-md-10 p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                         <h2>แก้ไขข้อมูลโรคพืช</h2>
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
                                                       class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</a></li>
                                   </ul>
                              </div>
                         </div>
                    </div>

                    <!-- Form -->
                    <div class="table-wrapper">
                         <div class="main-content p-4">
                              <form id="diseaseForm"
                                   action="http://localhost:8080/csP1015/project/plant_diseases/api.php" method="POST"
                                   enctype="multipart/form-data">
                                   <input type="hidden" name="id" value="<?php echo htmlspecialchars($data['id']); ?>">

                                   <div class="mb-3">
                                        <label for="disease_name_th" class="form-label">ชื่อโรค (ภาษาไทย)</label>
                                        <input type="text" class="form-control" name="disease_name_th"
                                             id="disease_name_th"
                                             value="<?php echo htmlspecialchars($data['disease_name_th']); ?>" required>
                                   </div>

                                   <div class="mb-3">
                                        <label for="disease_name_en" class="form-label">ชื่อโรค (ภาษาอังกฤษ)</label>
                                        <input type="text" class="form-control" name="disease_name_en"
                                             id="disease_name_en"
                                             value="<?php echo htmlspecialchars($data['disease_name_en']); ?>" required>
                                   </div>

                                   <div class="mb-3">
                                        <label for="cause" class="form-label">สาเหตุของโรค</label>
                                        <textarea class="form-control" name="cause" id="cause" rows="4"
                                             required><?php echo htmlspecialchars($data['cause']); ?></textarea>
                                   </div>

                                   <div class="mb-3">
                                        <label for="symptoms" class="form-label">อาการของโรค</label>
                                        <textarea class="form-control" name="symptoms" id="symptoms" rows="4"
                                             required><?php echo htmlspecialchars($data['symptoms']); ?></textarea>
                                   </div>

                                   <div class="mb-3">
                                        <label for="treatment" class="form-label">วิธีการรักษา</label>
                                        <textarea class="form-control" name="treatment" id="treatment" rows="4"
                                             required><?php echo htmlspecialchars($data['treatment']); ?></textarea>
                                   </div>

                                   <div class="mb-3">
                                        <label for="prevention" class="form-label">การป้องกัน</label>
                                        <textarea class="form-control" name="prevention" id="prevention" rows="4"
                                             required><?php echo htmlspecialchars($data['prevention']); ?></textarea>
                                   </div>

                                   <div class="mb-3">
                                        <label for="image" class="form-label">เลือกรูปภาพ</label>
                                        <input type="file" class="form-control mb-3" id="image" name="image"
                                             accept="image/*">
                                        <img src="../img/D/<?php echo str_pad($data['id'], 5, '0', STR_PAD_LEFT); ?>_1.jpg?v=<?php echo time(); ?>"
                                             width="500" alt="Current Image">
                                   </div>

                                   <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                              </form>
                         </div>
                    </div>
               </div>
          </div>
     </div>
     <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>
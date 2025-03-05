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
                         <h2>เพิ่มข้อมูลโรคพืช</h2>
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

                    <!-- Form -->
                    <div class="table-wrapper">
                         <div class="main-content p-4">
                              <form id="diseaseForm"
                                   action="http://localhost:8080/csP1015/project/plant_diseases/api.php" method="POST"
                                   enctype="multipart/form-data">
                                   <!-- กรณีแก้ไขข้อมูล (ซ่อน ID) -->
                                   <input type="hidden" name="id" id="diseaseId">

                                   <div class="mb-3">
                                        <label for="disease_name_th" class="form-label">ชื่อโรค (ภาษาไทย)</label>
                                        <input type="text" class="form-control" name="disease_name_th"
                                             id="disease_name_th" required>
                                   </div>

                                   <div class="mb-3">
                                        <label for="disease_name_en" class="form-label">ชื่อโรค (ภาษาอังกฤษ)</label>
                                        <input type="text" class="form-control" name="disease_name_en"
                                             id="disease_name_en" required>
                                   </div>

                                   <div class="mb-3">
                                        <label for="cause" class="form-label">สาเหตุของโรค</label>
                                        <textarea class="form-control" name="cause" id="cause" rows="4"
                                             required></textarea>
                                   </div>

                                   <!-- แก้ไขชื่อฟิลด์ symptom เป็น symptoms ให้ตรงกับ API -->
                                   <div class="mb-3">
                                        <label for="symptoms" class="form-label">อาการของโรค</label>
                                        <textarea class="form-control" name="symptoms" id="symptoms" rows="4"
                                             required></textarea>
                                   </div>

                                   <div class="mb-3">
                                        <label for="treatment" class="form-label">วิธีการรักษา</label>
                                        <textarea class="form-control" name="treatment" id="treatment" rows="4"
                                             required></textarea>
                                   </div>

                                   <div class="mb-3">
                                        <label for="prevention" class="form-label">การป้องกัน</label>
                                        <textarea class="form-control" name="prevention" id="prevention" rows="4"
                                             required></textarea>
                                   </div>

                                   <div class="mb-3">
                                        <label for="image" class="form-label">เลือกรูปภาพ</label>
                                        <input type="file" class="form-control" name="image" id="image"
                                             accept="image/*">
                                   </div>

                                   <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
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
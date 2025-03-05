<!DOCTYPE html>
<html lang="th">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>ระบบจัดการผู้ใช้งาน</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
     <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
     <link rel="icon" href="img/plant.png" type="image/png">
     <link rel="stylesheet" href="../css/style_v_add.css">
     <script>
          document.addEventListener('DOMContentLoaded', function () {
               const form = document.getElementById('userForm');
               form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    if (confirm("คุณต้องการบันทึกข้อมูลผู้ใช้หรือไม่?")) {
                         const formData = new FormData(form);

                         fetch('api.php', {
                              method: 'POST',
                              body: formData
                         })
                              .then(response => response.json())
                              .then(data => {
                                   alert(data.message);
                                   if (data.status === 'success' && data.redirect) {
                                        window.location.href = data.redirect;
                                   }
                              })
                              .catch(error => {
                                   console.error('Error:', error);
                                   alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล');
                              });
                    }
               });
          });
     </script>
     <script>
          document.getElementById("userForm").addEventListener("submit", function (event) {
               let password = document.querySelector("[name='password']").value;
               let image = document.querySelector("[name='image']").files[0];

               // ตรวจสอบความยาวรหัสผ่าน
               if (password.length < 6) {
                    alert("รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร");
                    event.preventDefault();
                    return;
               }

               // ตรวจสอบประเภทไฟล์รูปภาพ
               if (image) {
                    let allowedTypes = ["image/jpeg", "image/png"];
                    if (!allowedTypes.includes(image.type)) {
                         alert("อนุญาตเฉพาะไฟล์ JPG หรือ PNG เท่านั้น");
                         event.preventDefault();
                    }
               }
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
                         <h2>เพิ่มข้อมูลผู้ใช้</h2>
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
                              <form id="userForm" action="http://localhost:8080/csP1015/project/Users/api.php"
                                   method="POST" enctype="multipart/form-data">
                                   <!-- เพิ่มช่องซ่อน id สำหรับอัปเดตข้อมูล -->
                                   <input type="hidden" name="id" value="">

                                   <div class="mb-3">
                                        <label for="username" class="form-label">ชื่อผู้ใช้</label>
                                        <input type="text" class="form-control" name="username" required>
                                   </div>
                                   <div class="mb-3">
                                        <label for="password" class="form-label">รหัสผ่าน</label>
                                        <input type="password" class="form-control" name="password" minlength="6"
                                             required>
                                   </div>
                                   <div class="mb-3">
                                        <label for="email" class="form-label">อีเมล</label>
                                        <input type="email" class="form-control" name="email" required>
                                   </div>
                                   <div class="mb-3">
                                        <label for="full_name" class="form-label">ชื่อ-นามสกุล</label>
                                        <input type="text" class="form-control" name="full_name" required>
                                   </div>
                                   <div class="mb-3">
                                        <label for="phone_number" class="form-label">เบอร์โทรศัพท์</label>
                                        <input type="tel" class="form-control" name="phone_number" pattern="[0-9]{10}"
                                             title="กรุณากรอกเบอร์โทรศัพท์ 10 หลัก" required>
                                   </div>
                                   <div class="mb-3">
                                        <label for="image" class="form-label">รูปโปรไฟล์</label>
                                        <input type="file" class="form-control" name="image"
                                             accept="image/jpeg, image/png" required>
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
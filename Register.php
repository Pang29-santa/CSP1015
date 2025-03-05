<!DOCTYPE html>
<html lang="th">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>ลงทะเบียนผู้ใช้ใหม่</title>
     <!-- Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="Register.css">
     <style>
          .error-message {
               color: red;
               font-size: 0.875em;
               margin-top: 0.25rem;
          }

          .profile-preview {
               max-width: 200px;
               max-height: 200px;
               margin-top: 10px;
          }
     </style>
     <script>
          document.addEventListener('DOMContentLoaded', function () {
               const form = document.getElementById('userForm');
               form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    if (confirm("คุณต้องการบันทึกข้อมูลผู้ใช้หรือไม่?")) {
                         form.submit();
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

<body class="bg-light">
     <div class="container py-5">
          <div class="row justify-content-center">
               <div class="col-md-8 col-lg-6">
                    <div class="card shadow">
                         <div class="card-header bg-primary text-white">
                              <h3 class="card-title mb-0">ลงทะเบียนผู้ใช้ใหม่</h3>
                         </div>
                         <div class="card-body">
                              <form id="userForm" action="http://localhost:8080/csP1015/project/R_api.php"
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

     <!-- Bootstrap JS -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
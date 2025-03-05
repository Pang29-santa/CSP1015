<?php
session_start();

if (!isset($_SESSION['user_id'])) {
     header("Location: login.php");
     exit;
}
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
     <link rel="stylesheet" href="style_profile.css">
</head>

<body class="bg-light">
     <div id="alertContainer"></div>

     <div class="container profile-section">
          <!-- Add Back Button -->
          <div class="mb-3">
               <button onclick="goBack()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> กลับ
               </button>
          </div>

          <div class="card shadow">
               <div class="profile-header">
                    <div class="profile-avatar">
                         <img src="" id="profileImage" class="profile-image mb-3" alt="รูปโปรไฟล์">
                         <div class="camera-button" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                              <i class="bi bi-pencil-fill"></i>
                         </div>
                    </div>
                    <h3 class="text-white mt-3" id="userName"></h3>
                    <p class="text-white-50 mb-0" id="userRole">สมาชิก</p>
               </div>

               <div class="profile-info">
                    <div class="row">
                         <div class="col-md-6">
                              <div class="info-group">
                                   <div class="info-label">
                                        <i class="bi bi-person-circle"></i>
                                        ชื่อผู้ใช้
                                   </div>
                                   <div id="displayUsername" class="info-value"></div>
                              </div>
                         </div>
                         <div class="col-md-6">
                              <div class="info-group">
                                   <div class="info-label">
                                        <i class="bi bi-envelope"></i>
                                        อีเมล
                                   </div>
                                   <div id="displayEmail" class="info-value"></div>
                              </div>
                         </div>
                         <div class="col-md-6">
                              <div class="info-group">
                                   <div class="info-label">
                                        <i class="bi bi-person-badge"></i>
                                        ชื่อ-นามสกุล
                                   </div>
                                   <div id="displayFullName" class="info-value"></div>
                              </div>
                         </div>
                         <div class="col-md-6">
                              <div class="info-group">
                                   <div class="info-label">
                                        <i class="bi bi-telephone"></i>
                                        เบอร์โทรศัพท์
                                   </div>
                                   <div id="displayPhone" class="info-value"></div>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>

     <!-- Modal แก้ไขข้อมูล -->
     <div class="modal fade" id="editProfileModal" tabindex="-1">
          <div class="modal-dialog modal-lg">
               <div class="modal-content">
                    <div class="modal-header">
                         <h5 class="modal-title">แก้ไขข้อมูลส่วนตัว</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                         <form id="editProfileForm" enctype="multipart/form-data">
                              <div class="mb-3 text-center">
                                   <img src="" id="modalProfileImage" class="profile-image mb-3" alt="รูปโปรไฟล์">
                                   <div>
                                        <label for="profile_image" class="custom-file-upload">
                                             <i class="bi bi-camera-fill"></i> เปลี่ยนรูปโปรไฟล์
                                        </label>
                                        <input id="profile_image" type="file" name="profile_image" accept="image/*">
                                   </div>
                              </div>

                              <div class="row g-3">
                                   <div class="col-md-6">
                                        <label class="form-label">ชื่อผู้ใช้</label>
                                        <input type="text" name="username" class="form-control" readonly>
                                   </div>
                                   <div class="col-md-6">
                                        <label class="form-label">อีเมล</label>
                                        <input type="email" name="email" class="form-control" required>
                                   </div>
                                   <div class="col-md-6">
                                        <label class="form-label">ชื่อ-นามสกุล</label>
                                        <input type="text" name="full_name" class="form-control" required>
                                   </div>
                                   <div class="col-md-6">
                                        <label class="form-label">เบอร์โทรศัพท์</label>
                                        <input type="tel" name="phone_number" class="form-control" required>
                                   </div>
                              </div>
                         </form>
                    </div>
                    <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                         <button type="button" class="btn btn-primary"
                              onclick="saveProfile()">บันทึกการเปลี่ยนแปลง</button>
                    </div>
               </div>
          </div>
     </div>

     <!-- เพิ่ม Modal ยืนยันการเปลี่ยนรหัสผ่าน -->
     <div class="modal fade" id="confirmPasswordModal" tabindex="-1">
          <div class="modal-dialog">
               <div class="modal-content">
                    <div class="modal-header">
                         <h5 class="modal-title">ยืนยันการเปลี่ยนรหัสผ่าน</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                         <p>การเปลี่ยนรหัสผ่านจะทำให้คุณต้องเข้าสู่ระบบใหม่ ต้องการดำเนินการต่อหรือไม่?</p>
                    </div>
                    <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                         <button type="button" class="btn btn-primary" onclick="confirmChangePassword()">ยืนยัน</button>
                    </div>
               </div>
          </div>
     </div>

     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
     <script>
          document.addEventListener('DOMContentLoaded', loadUserProfile);

          function loadUserProfile() {
               fetch('http://localhost:8080/csP1015/project/api/user_profile.php')
                    .then(response => response.json())
                    .then(data => {
                         if (data.success) {
                              const user = data.data;
                              updateProfileDisplay(user);
                              updateModalFields(user);
                         }
                    })
                    .catch(error => {
                         console.error('Error:', error);
                         showAlert('error', 'ไม่สามารถโหลดข้อมูลได้');
                    });
          }

          function updateProfileDisplay(user) {
               document.getElementById('profileImage').src = user.profile_image;
               document.getElementById('modalProfileImage').src = user.profile_image;
               document.getElementById('userName').textContent = user.full_name;
               document.getElementById('displayUsername').textContent = user.username;
               document.getElementById('displayEmail').textContent = user.email;
               document.getElementById('displayFullName').textContent = user.full_name;
               document.getElementById('displayPhone').textContent = user.phone_number;
          }

          function updateModalFields(user) {
               const form = document.getElementById('editProfileForm');
               form.querySelector('[name="username"]').value = user.username;
               form.querySelector('[name="email"]').value = user.email;
               form.querySelector('[name="full_name"]').value = user.full_name;
               form.querySelector('[name="phone_number"]').value = user.phone_number;
          }

          function saveProfile() {
               const form = document.getElementById('editProfileForm');
               const formData = new FormData(form);
               const saveBtn = event.target;
               const originalText = saveBtn.innerHTML;
               showLoading(saveBtn);

               // แก้ไข URL ให้ถูกต้อง
               fetch('http://localhost:8080/csP1015/project/api/update_profile.php', {
                    method: 'POST',
                    body: formData
               })
                    .then(response => response.json())
                    .then(data => {
                         if (data.success) {
                              loadUserProfile(); // โหลดข้อมูลใหม่หลังจากอัพเดท
                              const modal = document.getElementById('editProfileModal');
                              const modalInstance = bootstrap.Modal.getInstance(modal);
                              modalInstance.hide();
                              showAlert('success', 'บันทึกข้อมูลสำเร็จ');
                         } else {
                              throw new Error(data.message);
                         }
                    })
                    .catch(error => {
                         console.error('Error:', error);
                         showAlert('error', error.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
                    })
                    .finally(() => {
                         hideLoading(saveBtn, originalText);
                    });
          }

          function showAlert(type, message) {
               const alertHtml = `
                    <div class="custom-alert alert alert-${type} alert-dismissible fade show">
                         <div class="d-flex align-items-center">
                              <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                              <div>${message}</div>
                         </div>
                         <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
               `;

               const alertContainer = document.getElementById('alertContainer');
               alertContainer.insertAdjacentHTML('beforeend', alertHtml);

               const alert = alertContainer.lastElementChild;
               setTimeout(() => alert.classList.add('show'), 100);
               setTimeout(() => {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 300);
               }, 3000);
          }

          // Image preview handler
          document.getElementById('profile_image').addEventListener('change', function (e) {
               const file = e.target.files[0];
               if (file) {
                    // ตรวจสอบประเภทไฟล์
                    if (!file.type.match('image.*')) {
                         showAlert('error', 'กรุณาเลือกไฟล์รูปภาพเท่านั้น');
                         return;
                    }

                    // ตรวจสอบขนาดไฟล์ (ไม่เกิน 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                         showAlert('error', 'ขนาดไฟล์ต้องไม่เกิน 5MB');
                         return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (e) {
                         document.getElementById('modalProfileImage').src = e.target.result;
                         document.getElementById('profileImage').src = e.target.result;
                    }
                    reader.readAsDataURL(file);
               }
          });

          // Add loading indicators
          function showLoading(element) {
               element.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    กำลังดำเนินการ...
               `; element.disabled = true;
          }

          function hideLoading(element, originalText) {
               element.innerHTML = originalText;
               element.disabled = false;
          }

          // Add back function
          function goBack() {
               if (document.referrer && document.referrer.includes(window.location.hostname)) {
                    window.history.back();
               } else {
                    window.location.href = 'index.php';
               }
          }
     </script>
</body>

</html>
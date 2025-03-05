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
     <title>แชร์กัน</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="../styles_post.css">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
     <link href="https://fonts.googleapis.com/css?family=Kanit&subset=thai,latin" rel="stylesheet" type="text/css">
     <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
     <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
     <style>
          .modal-xl {
               max-width: 90%;
               width: 1200px;
          }

          .post-detail-content {
               max-height: 80vh;
               overflow-y: auto;
          }

          .post-image-content img {
               max-height: 70vh !important;
               width: auto;
               margin: 0 auto;
               display: block;
          }

          .dropdown-item {
               cursor: pointer;
               padding: 8px 16px;
          }

          .dropdown-item:hover {
               background-color: #f8f9fa;
          }

          .btn-link {
               color: #6c757d;
               padding: 0;
          }

          .btn-link:hover {
               color: #343a40;
          }

          /* Quill Editor Styles */
          .ql-container {
               height: 200px;
               border-bottom-left-radius: 4px;
               border-bottom-right-radius: 4px;
          }

          .ql-toolbar {
               border-top-left-radius: 4px;
               border-top-right-radius: 4px;
               background: white;
          }

          .ql-editor {
               font-family: 'Kanit', sans-serif;
               font-size: 16px;
          }
     </style>
</head>

<body>
     <div class="nav-container" id="userNavContainer">
          <!-- User nav will be populated by JavaScript -->
     </div>

     <!-- แก้ไขส่วนแสดงโพสต์ทั้งหมด -->
     <div class="feed-container">
          <!-- ส่วนสร้างโพสต์ -->
          <div class="post-composer shadow-sm rounded bg-white p-3 mb-4">
               <form method="POST" enctype="multipart/form-data" id="postForm">
                    <div id="editor" class="mb-3"></div>
                    <input type="hidden" name="post_content" id="hiddenContent">
                    <div class="post-tools d-flex justify-content-between align-items-center">
                         <div class="d-flex gap-3">
                              <label class="action-btn d-flex align-items-center gap-2" role="button">
                                   <i class="bi bi-image"></i>
                                   <span>รูปภาพ</span>
                                   <input type="file" name="post_image" accept="image/*" style="display: none;"
                                        onchange="previewImage(this)">
                              </label>
                              <button type="button" class="btn btn-link text-decoration-none"
                                   onclick="toggleLinkInput()">
                                   <i class="bi bi-link-45deg"></i>
                                   <span>เพิ่มลิงก์</span>
                              </button>
                         </div>
                         <button type="submit" class="btn btn-primary px-4">
                              โพสต์ <i class="bi bi-send ms-2"></i>
                         </button>
                    </div>

                    <!-- ส่วนแสดงตัวอย่างรูปภาพและลิงก์ -->
                    <div id="image-preview-container" class="mt-3" style="display: none;">
                         <div class="position-relative d-inline-block">
                              <img id="image-preview" src="#" alt="Preview"
                                   style="max-width: 200px; max-height: 200px; object-fit: contain;">
                              <button type="button" class="btn-close position-absolute top-0 end-0"
                                   onclick="removeImage()" style="background-color: white; padding: 5px;"></button>
                         </div>
                    </div>

                    <div id="link-input-container" class="mt-3" style="display: none;">
                         <div class="input-group">
                              <input type="url" class="form-control" id="post-link" name="post_link"
                                   placeholder="วางลิงก์ของคุณที่นี่ (https://...)">
                              <button type="button" class="btn btn-outline-secondary" onclick="removeLinkInput()">
                                   <i class="bi bi-x"></i>
                              </button>
                         </div>
                    </div>
               </form>
          </div>

          <!-- ส่วนแสดงโพสต์ -->
          <div class="posts-feed">
               <!-- Posts will be loaded here by JavaScript -->
          </div>
     </div>

     <div class="footer-space"></div>

     <!-- Edit Post Modal -->
     <div class="modal fade" id="editPostModal" tabindex="-1" aria-labelledby="editPostModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg"> <!-- Changed to modal-lg for larger modal -->
               <div class="modal-content">
                    <div class="modal-header">
                         <h5 class="modal-title" id="editPostModalLabel">แก้ไขโพสต์</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                         <form id="editPostForm" enctype="multipart/form-data">
                              <div class="mb-3">
                                   <label class="form-label">ข้อความ</label>
                                   <div id="editEditor"></div>
                                   <input type="hidden" id="editPostContent">
                              </div>
                              <div class="mb-3">
                                   <label class="form-label d-block">รูปภาพ</label>
                                   <div class="d-flex flex-column align-items-center gap-2">
                                        <div id="editImagePreview" class="mb-2" style="display: none;">
                                             <img src="" alt="Preview"
                                                  style="max-width: 100%; max-height: 400px; width: auto; height: auto; object-fit: contain;">
                                        </div>
                                        <div class="d-flex gap-2">
                                             <button type="button" class="btn btn-outline-primary btn-sm"
                                                  id="changeImage">
                                                  <i class="bi bi-image"></i> เปลี่ยนรูปภาพ
                                             </button>
                                             <input type="file" id="editPostImage" style="display: none;"
                                                  accept="image/*">
                                             <button type="button" class="btn btn-outline-danger btn-sm"
                                                  id="removeImage" style="display: none;">
                                                  <i class="bi bi-trash"></i> ลบรูปภาพ
                                             </button>
                                        </div>
                                   </div>
                              </div>
                              <div class="mb-3">
                                   <label for="editPostLink" class="form-label">ลิงก์</label>
                                   <input type="url" class="form-control" id="editPostLink" placeholder="https://...">
                              </div>
                         </form>
                    </div>
                    <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                         <button type="button" class="btn btn-primary" id="saveEditPost">บันทึก</button>
                    </div>
               </div>
          </div>
     </div>

     <!-- Add Post Detail Modal -->
     <div class="modal fade" id="postDetailModal" tabindex="-1">
          <div class="modal-dialog modal-xl"> <!-- Changed from modal-lg to modal-xl -->
               <div class="modal-content">
                    <div class="modal-header">
                         <div class="d-flex align-items-center gap-3"> <!-- Increased gap -->
                              <img src="" alt="Profile" class="post-user-img rounded-circle" width="50" height="50">
                              <!-- Increased size -->
                              <div>
                                   <h5 class="mb-0 post-user-name"></h5> <!-- Changed from h6 to h5 -->
                                   <small class="text-muted post-date"></small>
                              </div>
                         </div>
                         <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body post-detail-content">
                         <div class="post-text-content mb-3"></div>
                         <div class="post-image-content text-center mb-3" style="display: none;">
                              <img src="" alt="Post Image" class="img-fluid rounded" style="max-height: 500px;">
                         </div>
                         <div class="d-flex gap-2 mb-3">
                              <button class="btn btn-sm btn-outline-danger modal-like-btn">
                                   <i class="bi bi-heart"></i>
                                   <span>0</span>
                              </button>
                              <button class="btn btn-sm btn-warning">
                                   <i class="bi bi-chat"></i>
                                   <span class="modal-comments-count">0</span>
                              </button>
                         </div>
                         <hr>
                         <h6 class="mb-3">ความคิดเห็น</h6>
                         <div class="comments-section"></div>
                         <form onsubmit="return handleModalComment(event)" class="mt-3">
                              <div class="input-group">
                                   <input type="text" class="form-control" placeholder="แสดงความคิดเห็น..." required>
                                   <button class="btn btn-primary" type="submit">ส่ง</button>
                              </div>
                         </form>
                    </div>
               </div>
          </div>
     </div>

     <!-- Modal ยืนยันการลบโพสต์ -->
     <div class="modal fade" id="deletePostModal" tabindex="-1" aria-labelledby="deletePostModalLabel"
          aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
               <div class="modal-content">
                    <div class="modal-header">
                         <h5 class="modal-title" id="deletePostModalLabel">ยืนยันการลบโพสต์</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                         คุณแน่ใจหรือไม่ว่าต้องการลบโพสต์นี้?
                    </div>
                    <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                         <button type="button" class="btn btn-danger" id="confirmDelete">ลบโพสต์</button>
                    </div>
               </div>
          </div>
     </div>

     <!-- Modal ยืนยันการโพสต์ -->
     <div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel"
          aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
               <div class="modal-content">
                    <div class="modal-header">
                         <h5 class="modal-title" id="createPostModalLabel">ยืนยันการโพสต์</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                         คุณแน่ใจหรือไม่ว่าต้องการโพสต์นี้?
                    </div>
                    <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                         <button type="button" class="btn btn-primary" id="confirmPost">โพสต์</button>
                    </div>
               </div>
          </div>
     </div>

     <!-- Modal ยืนยันการแก้ไขโพสต์ -->
     <div class="modal fade" id="confirmEditModal" tabindex="-1" aria-labelledby="confirmEditModalLabel"
          aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
               <div class="modal-content">
                    <div class="modal-header">
                         <h5 class="modal-title" id="confirmEditModalLabel">ยืนยันการแก้ไขโพสต์</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                         คุณแน่ใจหรือไม่ว่าต้องการแก้ไขโพสต์นี้?
                    </div>
                    <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                         <button type="button" class="btn btn-primary" id="confirmEditButton">ยืนยัน</button>
                    </div>
               </div>
          </div>
     </div>


     <!-- Modal ยืนยันการลบความคิดเห็น -->
     <div class="modal fade" id="deleteCommentModal" tabindex="-1" aria-labelledby="deleteCommentModalLabel"
          aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
               <div class="modal-content">
                    <div class="modal-header">
                         <h5 class="modal-title" id="deleteCommentModalLabel">ยืนยันการลบความคิดเห็น</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">คุณแน่ใจหรือไม่ว่าต้องการลบความคิดเห็นนี้?</div>
                    <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                         <button type="button" class="btn btn-danger" id="confirmDeleteComment">ลบ</button>
                    </div>
               </div>
          </div>
     </div>

     <!-- ย้าย script ขึ้นมาไว้ก่อน </body> และรวม script ทั้งหมด -->
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
     <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

     <script>
          $(document).ready(function () {
               // Global error handler
               $(document).ajaxError(function (event, jqxhr, settings, error) {
                    console.error('Ajax Error:', error);
                    alert('เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองใหม่อีกครั้ง');
               });

               // Initialize
               loadUserData();
               loadPosts();
               setupEventHandlers();
          });

          function loadUserData() {
               $.ajax({
                    url: 'http://localhost:8080/csP1015/project/post_user/api_user.php',
                    method: 'GET',
                    success: function (response) {
                         if (response.success) {
                              const user = response.data;
                              // Update navigation with user data
                              updateUserNavigation(user);
                         } else {
                              console.error('Failed to load user data:', response.message);
                         }
                    },
                    error: function (xhr, status, error) {
                         console.error('Error loading user data:', error);
                    }
               });
          }

          function loadPosts() {
               $.ajax({
                    url: 'http://localhost:8080/csP1015/project/post_user/api_posts.php',
                    method: 'GET',
                    success: function (response) {
                         if (response.success) {
                              displayPosts(response.data);
                         } else {
                              console.error('Failed to load posts:', response.message);
                         }
                    },
                    error: function (xhr, status, error) {
                         console.error('Error loading posts:', error);
                    }
               });
          }

          function setupEventHandlers() {
               // Post interaction handlers
               $(document).on('click', '.post-content', handlePostClick);
               $(document).on('click', '.like-btn', handleLikeClick);
               $(document).on('click', '.comment-btn', handleCommentClick);
               $(document).on('click', '.edit-post', handleEditClick);
               $(document).on('click', '.delete-post', handleDeleteClick);

               // Form handlers - แก้ไขส่วนนี้
               $('#postForm').on('submit', function (e) {
                    e.preventDefault();
                    confirmPost(e);
               });

               $('#editPostForm').on('submit', handleEditSubmit);

               // Image handlers
               $('input[type="file"]').on('change', handleImagePreview);
               $('#removeImage').on('click', handleImageRemove);

               // เพิ่ม handler สำหรับปุ่มบันทึกในโมดัลแก้ไข
               $('#saveEditPost').on('click', function () {
                    $('#editPostForm').submit();
               });

          }

          function displayPosts(posts) {
               const feedContainer = $('.posts-feed');
               let postsHtml = '';
               const timestamp = new Date().getTime(); // Add timestamp for cache busting

               posts.forEach(post => {
                    // Add timestamp to image URL to prevent caching
                    const userImage = `http://localhost:8080/csP1015/project/img/U/${String(post.user_id).padStart(5, '0')}_1.jpg?t=${timestamp}`;

                    // แปลงลิงก์ในเนื้อหาให้คลิกได้post.user_id == <?php echo $_SESSION['user_id']; ?> ? `
                    let processedContent = post.content.replace(/ลิงก์: (https?:\/\/[^\s]+)/g, 'ลิงก์: <a href="$1" target="_blank" class="text-primary" onclick="event.stopPropagation();">$1</a>');

                    postsHtml += `
                <div class="post-card shadow-sm rounded mb-4" data-post-id="${post.post_id}">
                    <div class="post-header p-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <img src="${userImage}" onerror="this.src='../image/default_profile.jpg'"
                                    alt="Profile" class="rounded-circle" width="45" height="45">
                                <div>
                                    <h6 class="mb-0 fw-semibold">${post.full_name}</h6>
                                    <small class="text-muted">${new Date(post.created_at).toLocaleString()}</small>
                                </div>
                            </div>
                        </div>
                                                    ${post.user_id == <?php echo $_SESSION['user_id']; ?> ? `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button class="dropdown-item edit-post" 
                                            data-post-id="${post.post_id}" 
                                            data-content="${encodeURIComponent(JSON.stringify(post))}">
                                            <i class="bi bi-pencil me-2"></i>แก้ไขโพสต์
                                        </button>
                                    </li>
                                    <li><button class="dropdown-item text-danger delete-post" data-post-id="${post.post_id}">
                                        <i class="bi bi-trash me-2"></i>ลบโพสต์
                                    </button></li>
                                </ul>
                            </div>` : ''}
                    </div>
                    <div class="post-content p-3" style="cursor: pointer;">
                        <div class="content-text mb-3">${processedContent}</div>
                        ${post.image_path ? `
                            <div class="post-image-container">
                                <img src="http://localhost:8080${post.image_path}" class="img-fluid rounded"
                                    style="max-height: 500px; width: 100%; object-fit: cover;">
                            </div>
                        ` : ''}
                    </div>
                    <div class="post-actions p-3 border-top">
                        <div class="d-flex gap-3">
                            <button class="btn btn-sm ${post.user_liked ? 'btn-danger' : 'btn-outline-danger'} like-btn"
                                data-post-id="${post.post_id}">
                                <i class="bi bi-heart${post.user_liked ? '-fill' : ''} me-1"></i>
                                <span class="likes-count">${post.likes_count}</span>
                            </button>
                            <button class="btn btn-sm btn-warning comment-btn"
                                data-post-id="${post.post_id}">
                                <i class="bi bi-chat me-1"></i>
                                <span>${post.comments_count}</span>
                            </button>
                        </div>
                    </div>
                </div>
                         `;
               });

               feedContainer.html(postsHtml);
               // เพิ่ม event handler สำหรับลิงก์
               $('.content-text a').on('click', function (e) {
                    e.stopPropagation();
                    return true; // อนุญาตให้เปิดลิงก์ในแท็บใหม่
               });
          }

          function updateUserNavigation(user) {
               const navHtml = `
            <nav class="navbar">
                <div class="logo">
                    <img src="../image/vegetables.png" alt="Logo">
                </div>
                <ul class="nav-links">
                    <li><a href="../index.php">หน้าหลัก</a></li>
                    <li><a href="../vegetable.php">ข้อมูลพืชผักสวนครัว</a></li>
                    <li><a href="../diseases.php">ข้อมูลโรคพืช</a></li>
                    <li><a href="../Pest.php">ข้อมูลแมลงศัตรูพืช</a></li>
                    <li><a href="#">แชร์กัน</a></li>
                </ul>
                <div class="user-profile">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="${user.profile_image}" alt="Profile Image"
                                class="rounded-circle" width="60px" height="60px">
                            ${user.full_name}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../profile.php">โปรไฟล์</a></li>
                            <li><a class="dropdown-item" href="../index.html"
                                    onclick="return confirm('คุณต้องการออกจากระบบหรือไม่?');">ออกจากระบบ</a></li>
                        </ul>
                    </li>
                </div>
            </nav>`;
               $('#userNavContainer').html(navHtml);
          }

          function handlePostClick(e) {
               if (!$(e.target).is('a')) { // Don't trigger for link clicks
                    const postCard = $(this).closest('.post-card');
                    showPostDetail(postCard);
               }
          }

          function handleLikeClick(e) {
               e.preventDefault();
               e.stopPropagation();
               const btn = $(this);
               const postId = btn.closest('.post-card').data('post-id') || $('#postDetailModal').data('post-id');
               handleLike(btn, postId);
          }

          function handleCommentClick(e) {
               e.preventDefault();
               e.stopPropagation();
               const postId = $(this).data('post-id');
               handleComment(postId);
          }

          function handleEditClick(e) {
               e.preventDefault();
               e.stopPropagation();
               const postId = $(this).data('post-id');
               const postData = JSON.parse(decodeURIComponent($(this).data('content')));

               try {
                    // กำหนดค่าข้อมูลโดยตรงจาก postData แทนการเรียก API
                    let content = postData.content;
                    const linkMatch = content.match(/ลิงก์: (https?:\/\/[^\s]+)/);

                    if (linkMatch) {
                         content = content.replace(/\n*ลิงก์: https?:\/\/[^\s]+/, '').trim();
                         $('#editPostLink').val(linkMatch[1]);
                    } else {
                         $('#editPostLink').val('');
                    }

                    // Set content to Quill editor
                    editQuill.root.innerHTML = content;

                    // Set image if exists
                    if (postData.image_path) {
                         $('#editImagePreview img').attr('src', 'http://localhost:8080' + postData.image_path);
                         $('#editImagePreview').show();
                         $('#removeImage').show();
                    } else {
                         $('#editImagePreview').hide();
                         $('#removeImage').hide();
                    }

                    // Store post ID and original image path
                    $('#editPostModal').data('post-id', postId);
                    $('#editPostModal').data('original-image', postData.image_path || '');

                    // Show modal
                    $('#editPostModal').modal('show');
               } catch (error) {
                    console.error('Error handling edit data:', error);
                    alert('เกิดข้อผิดพลาดในการโหลดข้อมูล');
               }
          }

          function handleDeleteClick(e) {
               e.preventDefault();
               e.stopPropagation();
               const postId = $(this).data('post-id');
               handleDelete(postId);
          }

          function handleEditSubmit(e) {
               e.preventDefault();

               // Show confirmation modal instead of submitting directly
               $('#confirmEditModal').modal('show');

               // Handle confirmation button click
               $('#confirmEditButton').off('click').on('click', function () {
                    const formData = new FormData();
                    const postId = $('#editPostModal').data('post-id');

                    // เพิ่มข้อมูลพื้นฐาน
                    formData.append('post_id', postId);

                    // Get content from Quill editor
                    const content = editQuill.root.innerHTML;
                    if (!content || editQuill.getText().trim().length === 0) {
                         alert('กรุณากรอกเนื้อหาโพสต์');
                         return false;
                    }
                    formData.append('content', content);

                    // จัดการลิงก์
                    const link = $('#editPostLink').val().trim();
                    if (link) {
                         formData.append('post_link', link);
                    }

                    // จัดการรูปภาพ
                    const newImage = $('#editPostImage')[0].files[0];
                    const isRemoving = $('#editImagePreview').is(':hidden');
                    const hasOriginalImage = $('#editPostModal').data('original-image');

                    if (newImage) {
                         formData.append('post_image', newImage);
                    } else if (isRemoving && hasOriginalImage) {
                         formData.append('remove_image', '1');
                    }

                    // แสดง loading state
                    const saveBtn = $('#confirmEditButton');
                    saveBtn.prop('disabled', true)
                         .html('<span class="spinner-border spinner-border-sm"></span> กำลังบันทึก...');

                    // ส่งข้อมูล
                    $.ajax({
                         url: 'http://localhost:8080/csP1015/project/post_user/edit_post.php',
                         method: 'POST',
                         data: formData,
                         processData: false,
                         contentType: false,
                         success: function (response) {
                              if (response.success) {
                                   resetEditForm();
                                   $('#editPostModal').modal('hide');
                                   $('#confirmEditModal').modal('hide');
                                   loadPosts();
                              } else {
                                   alert('เกิดข้อผิดพลาด: ' + response.message);
                              }
                         },
                         error: function (xhr, status, error) {
                              console.error('Ajax error:', error);
                              alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                         },
                         complete: function () {
                              saveBtn.prop('disabled', false).text('ยืนยัน');
                         }
                    });
               });
          }

          // แก้ไขการจัดการรูปภาพในฟอร์มแก้ไข
          $('#editPostImage').on('change', function (e) {
               if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                         $('#editImagePreview img').attr('src', e.target.result);
                         $('#editImagePreview').show();
                         $('#removeImage').show();
                    };
                    reader.readAsDataURL(e.target.files[0]);
               }
          });

          $('#removeImage').on('click', function () {
               $('#editPostImage').val('');
               $('#editImagePreview').hide();
               $(this).hide();
               // เพิ่มการทำเครื่องหมายว่าต้องการลบรูปภาพ
               $('#editPostModal').data('remove-image', true);
          });

          // เพิ่ม handler สำหรับปุ่มเปลี่ยนรูปภาพ
          $('#changeImage').on('click', function () {
               $('#editPostImage').click();
          });

          function handleImagePreview(e) {
               if (e.target.files && e.target.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function (e) {
                         $('#image-preview').attr('src', e.target.result);
                         $('#image-preview-container').show();
                    }
                    reader.readAsDataURL(e.target.files[0]);
               }
          }

          function handleImageRemove() {
               $('#image-preview').attr('src', '#');
               $('#image-preview-container').hide();
               $('input[name="post_image"]').val('');
          }

          $('#removeImage').on('click', function () {
               $('#editPostImage').val('');
               $('#editImagePreview').hide();
               $(this).hide();
               // เพิ่มการทำเครื่องหมายว่าต้องการลบรูปภาพ
               $('#editPostModal').data('remove-image', true);
          });

          function handleLike(btn, postId) {
               if (!postId) {
                    console.error('Post ID is missing');
                    return;
               }

               $.ajax({
                    url: 'http://localhost:8080/csP1015/project/post_user/like_post.php',
                    method: 'POST',
                    data: { post_id: postId },
                    dataType: 'json',
                    success: function (response) {
                         if (response.success) {
                              // หาปุ่มไลค์ทั้งในโพสต์หลักและใน modal
                              const mainBtn = $(`.post-card[data-post-id="${postId}"] .like-btn`);
                              const modalBtn = $('#postDetailModal .modal-like-btn');
                              const newCount = response.likes_count;

                              if (response.action === 'liked') {
                                   // กรณีกดไลค์
                                   mainBtn.removeClass('btn-outline-danger').addClass('btn-danger')
                                        .find('i').removeClass('bi-heart').addClass('bi-heart-fill');
                                   modalBtn.removeClass('btn-outline-danger').addClass('btn-danger')
                                        .find('i').removeClass('bi-heart').addClass('bi-heart-fill');
                              } else {
                                   // กรณียกเลิกไลค์
                                   mainBtn.removeClass('btn-danger').addClass('btn-outline-danger')
                                        .find('i').removeClass('bi-heart-fill').addClass('bi-heart');
                                   modalBtn.removeClass('btn-danger').addClass('btn-outline-danger')
                                        .find('i').removeClass('bi-heart-fill').addClass('bi-heart');
                              }

                              // อัพเดตจำนวนไลค์ทั้งในหน้าหลักและ modal
                              mainBtn.find('.likes-count').text(newCount);
                              modalBtn.find('span').text(newCount);

                              // อัพเดตข้อมูล data-likes ใน modal
                              $('#postDetailModal').data('likes', newCount);
                         } else {
                              console.error('Like failed:', response.message);
                              alert('เกิดข้อผิดพลาดในการกดไลค์: ' + response.message);
                         }
                    },
                    error: function (xhr, status, error) {
                         console.error('Ajax error:', error);
                         alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                    }
               });
          }

          function handleDelete(postId) {
               if (!postId) return;

               // แสดง modal ยืนยันการลบ
               const deleteModal = $('#deletePostModal');
               deleteModal.data('post-id', postId);
               deleteModal.modal('show');

               // เพิ่ม event handler สำหรับปุ่มยืนยันการลบ
               $('#confirmDelete').off('click').on('click', function () {
                    $.ajax({
                         url: 'http://localhost:8080/csP1015/project/post_user/delete_post.php',
                         method: 'POST',
                         data: {
                              post_id: postId,
                              user_id: <?php echo $user_id; ?>
                         },
                         success: function (response) {
                              if (response.success) {
                                   $(`.post-card[data-post-id="${postId}"]`).fadeOut(400, function () {
                                        $(this).remove();
                                   });
                                   deleteModal.modal('hide');
                                   $('#postDetailModal').modal('hide');
                              } else {
                                   alert('ไม่สามารถลบโพสต์ได้: ' + (response.message || 'เกิดข้อผิดพลาด'));
                              }
                         },
                         error: function () {
                              alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                         }
                    });
               });
          }

          function showPostDetail(postCard) {
               const modal = $('#postDetailModal');
               const postId = $(postCard).data('post-id');

               // แก้ไขการดึงข้อมูล
               const userImg = $(postCard).find('.post-header .rounded-circle').attr('src');
               const userName = $(postCard).find('.post-header h6.fw-semibold').text().trim();
               const postDate = $(postCard).find('.post-header small.text-muted').text().trim();
               const postContent = $(postCard).find('.content-text').html();
               const postImage = $(postCard).find('.post-image-container img').attr('src');
               const likesCount = $(postCard).find('.like-btn .likes-count').text();
               const likeStatus = $(postCard).find('.like-btn').hasClass('btn-danger');
               const commentsCount = $(postCard).find('.comment-btn span').text();

               // ใส่ข้อมูลใน modal
               modal.find('.post-user-img').attr('src', userImg || '../img/default-profile.jpg');
               modal.find('.post-user-name').text(userName);
               modal.find('.post-date').text(postDate);
               modal.find('.post-text-content').html(postContent || '');

               // จัดการรูปภาพ
               const modalImage = modal.find('.post-image-content');
               if (postImage) {
                    modalImage.find('img').attr('src', postImage);
                    modalImage.show();
               } else {
                    modalImage.hide();
               }

               // อัพเดทจำนวนไลค์และคอมเมนต์
               modal.find('.modal-like-btn span').text(likesCount);
               modal.find('.modal-comments-count').text(commentsCount);

               // เก็บ post_id
               modal.data('post-id', postId);

               // โหลดคอมเมนต์
               loadModalComments(postId);

               // อัพเดตสถานะปุ่มไลค์
               const likeBtn = $(postCard).find('.like-btn');
               const modalLikeBtn = modal.find('.modal-like-btn');
               modalLikeBtn.data('post-id', postId); // เพิ่มบรรทัดนี้
               if (likeBtn.hasClass('btn-danger')) {
                    modalLikeBtn.removeClass('btn-outline-danger').addClass('btn-danger').find('i').removeClass('bi-heart').addClass('bi-heart-fill');
               } else {
                    modalLikeBtn.removeClass('btn-danger').addClass('btn-outline-danger').find('i').removeClass('bi-heart-fill').addClass('bi-heart');
               }

               // เพิ่ม event handler สำหรับปุ่มไลค์ใน modal
               modalLikeBtn.off('click').on('click', function (e) {
                    e.preventDefault();
                    handleLike($(this), postId);
               });

               modal.modal('show');
          }

          function loadModalComments(postId) {
               $.ajax({
                    url: 'http://localhost:8080/csP1015/project/post_user/api_comments.php',
                    method: 'GET',
                    data: { post_id: postId },
                    success: function (response) {
                         if (response.success) {
                              let html = '';
                              const timestamp = new Date().getTime(); // เพิ่ม timestamp
                              response.comments.forEach(comment => {
                                   const profileImage = `http://localhost:8080/csP1015/project/img/U/${String(comment.user_id).padStart(5, '0')}_1.jpg?t=${timestamp}`; // เพิ่ม timestamp ที่ URL รูปภาพ
                                   const isCurrentUser = comment.user_id == <?php echo $_SESSION['user_id']; ?>;

                                   html += `
                        <div class="comment-item d-flex gap-2 mb-3" data-comment-id="${comment.comment_id}">
                            <img src="${profileImage}" onerror="this.src='../image/default_profile.jpg'" 
                                class="rounded-circle" width="32" height="32">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>${comment.full_name}</strong>
                                        <small class="text-muted ms-2">${comment.formatted_date}</small>
                                    </div>
                                    ${isCurrentUser ? `
                                        <button type="button" class="btn btn-sm btn-link text-danger delete-comment p-0" 
                                            onclick="showDeleteCommentModal('${comment.comment_id}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    ` : ''}
                                </div>
                                <p class="mb-0 comment-text">${comment.comment_text}</p>
                            </div>
                        </div>`;
                              });
                              $('#postDetailModal .comments-section').html(html || '<p class="text-center text-muted">ยังไม่มีความคิดเห็น</p>');
                         }
                    }
               });
          }

          // Add this new function to handle showing the delete modal
          function showDeleteCommentModal(commentId) {
               const deleteModal = $('#deleteCommentModal');
               deleteModal.data('comment-id', commentId);
               deleteModal.modal('show');
          }

          // Update the delete comment confirmation handler
          $(document).ready(function () {
               // ...existing code...

               $('#confirmDeleteComment').on('click', function () {
                    const deleteModal = $('#deleteCommentModal');
                    const commentId = deleteModal.data('comment-id');
                    const deleteBtn = $(this);

                    deleteBtn.prop('disabled', true)
                         .html('<span class="spinner-border spinner-border-sm"></span> กำลังลบ...');

                    $.ajax({
                         url: 'http://localhost:8080/csP1015/project/post_user/delete_comment.php',
                         method: 'POST',
                         data: JSON.stringify({
                              comment_id: commentId
                         }),
                         contentType: 'application/json',
                         success: function (response) {
                              if (response.success) {
                                   const postId = $('#postDetailModal').data('post-id');
                                   loadModalComments(postId);

                                   const currentCount = parseInt($('.modal-comments-count').text());
                                   const newCount = currentCount - 1;
                                   $('.modal-comments-count').text(newCount);
                                   $(`.comment-btn[data-post-id="${postId}"] span`).text(newCount);

                                   deleteModal.modal('hide');
                              } else {
                                   alert('ไม่สามารถลบความคิดเห็นได้');
                              }
                         },
                         error: function () {
                              alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                         },
                         complete: function () {
                              deleteBtn.prop('disabled', false).text('ลบ');
                         }
                    });
               });
          });

          function setupCommentHandlers() {
               // Delete comment handler
               $('.delete-comment').off('click').on('click', function () {
                    const commentId = $(this).data('comment-id');
                    if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบความคิดเห็นนี้?')) {
                         deleteComment(commentId);
                    }
               });
          }

          function deleteComment(commentId) {
               // Show delete confirmation modal
               const deleteModal = $('#deleteCommentModal');
               deleteModal.modal('show');

               // Handle delete confirmation
               $('#confirmDeleteComment').off('click').on('click', function () {
                    const deleteBtn = $(this);

                    // Show loading state
                    deleteBtn.prop('disabled', true)
                         .html('<span class="spinner-border spinner-border-sm"></span> กำลังลบ...');

                    $.ajax({
                         url: 'http://localhost:8080/csP1015/project/post_user/delete_comment.php',
                         method: 'POST',
                         data: JSON.stringify({
                              comment_id: commentId
                         }),
                         contentType: 'application/json',
                         success: function (response) {
                              if (response.success) {
                                   const postId = $('#postDetailModal').data('post-id');
                                   loadModalComments(postId);

                                   // Update comment count
                                   const currentCount = parseInt($('.modal-comments-count').text());
                                   const newCount = currentCount - 1;
                                   $('.modal-comments-count').text(newCount);
                                   $(`.comment-btn[data-post-id="${postId}"] span`).text(newCount);

                                   // Hide the delete confirmation modal
                                   deleteModal.modal('hide');
                              } else {
                                   alert('ไม่สามารถลบความคิดเห็นได้');
                              }
                         },
                         error: function () {
                              alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                         },
                         complete: function () {
                              deleteBtn.prop('disabled', false).text('ลบ');
                         }
                    });
               });
          }

          function handleComment(postId) {
               // Function to handle comment click
          }

          function handleModalComment(event) {
               event.preventDefault();
               const modal = $('#postDetailModal');
               const postId = modal.data('post-id');
               const input = modal.find('input');
               const comment = input.val().trim();
               if (!comment) return false;

               $.ajax({
                    url: 'http://localhost:8080/csP1015/project/post_user/api_comments.php',
                    method: 'POST',
                    data: JSON.stringify({
                         post_id: postId,
                         comment_text: comment
                    }),
                    contentType: 'application/json',
                    success: function (response) {
                         if (response.success) {
                              input.val('');
                              loadModalComments(postId);
                              // อัพเดทจำนวนคอมเมนต์ในการ์ดและ modal
                              const newCount = parseInt($('.modal-comments-count').text()) + 1;
                              $('.modal-comments-count').text(newCount);
                              $(`.comment-btn[data-post-id="${postId}"] span`).text(newCount);
                         }
                    }
               });
               return false;
          }

          function confirmPost(event) {
               event.preventDefault();
               const content = quill.root.innerHTML;

               if (!content || quill.getText().trim().length === 0) {
                    alert('กรุณากรอกเนื้อหาโพสต์');
                    return false;
               }

               $('#hiddenContent').val(content);

               const form = document.getElementById('postForm');
               const linkInput = document.getElementById('post-link');
               if (linkInput && linkInput.value.trim() && !isValidUrl(linkInput.value.trim())) {
                    alert('กรุณาใส่ลิงก์ที่ถูกต้อง');
                    return false;
               }

               // แสดง modal ยืนยันการโพสต์
               const createModal = $('#createPostModal');
               createModal.modal('show');

               // เพิ่ม event handler สำหรับปุ่มยืนยันการโพสต์
               $('#confirmPost').off('click').on('click', function () {
                    const formData = new FormData(form);
                    const submitBtn = createModal.find('#confirmPost');
                    const originalText = submitBtn.text();

                    submitBtn.prop('disabled', true)
                         .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> กำลังโพสต์...');

                    $.ajax({
                         url: 'http://localhost:8080/csP1015/project/post_user/api.php',
                         method: 'POST',
                         data: formData,
                         processData: false,
                         contentType: false,
                         success: function (response) {
                              if (response.success) {
                                   resetForm();
                                   createModal.modal('hide');
                                   loadPosts();
                              } else {
                                   alert('เกิดข้อผิดพลาด: ' + (response.message || 'ไม่สามารถสร้างโพสต์ได้'));
                              }
                         },
                         error: function () {
                              alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                         },
                         complete: function () {
                              submitBtn.prop('disabled', false).text(originalText);
                         }
                    });
               });

               return false;
          }

          function isValidUrl(string) {
               try {
                    const url = new URL(string);
                    return url.protocol === "http:" || url.protocol === "https:";
               } catch (_) {
                    return false;
               }
          }

          function toggleLinkInput() {
               const linkContainer = document.getElementById('link-input-container');
               linkContainer.style.display = linkContainer.style.display === 'none' ? 'block' : 'none';
               if (linkContainer.style.display === 'block') {
                    document.getElementById('post-link').focus();
               }
          }

          function removeLinkInput() {
               document.getElementById('link-input-container').style.display = 'none';
               document.getElementById('post-link').value = '';
          }

          function handleEdit(postId, postData) {
               $('#editPostModal').modal('show');

               // Reset form
               $('#editPostForm')[0].reset();
               $('#editImagePreview').hide();
               $('#removeImage').hide();

               // Set up image change handler
               $('#changeImage').off('click').on('click', function () {
                    $('#editPostImage').click();
               });

               // Set up image preview handler 
               $('#editPostImage').off('change').on('change', function (e) {
                    if (e.target.files && e.target.files[0]) {
                         let reader = new FileReader();
                         reader.onload = function (e) {
                              $('#editImagePreview img').attr('src', e.target.result);
                              $('#editImagePreview').show();
                              $('#removeImage').show();
                         }
                         reader.readAsDataURL(e.target.files[0]);
                    }
               });

               // Set up remove image handler
               $('#removeImage').off('click').on('click', function () {
                    $('#editPostImage').val('');
                    $('#editImagePreview').hide();
                    $(this).hide();
               });

               try {
                    // กำหนดค่าข้อมูลเดิม
                    let postContent = postData.content;
                    const linkMatch = postData.content.match(/ลิงก์: (https?:\/\/[^\s]+)/);

                    if (linkMatch) {
                         postContent = postData.content.replace(/\n*ลิงก์: https?:\/\/[^\s]+/, '').trim();
                         $('#editPostLink').val(linkMatch[1]);
                    } else {
                         $('#editPostLink').val('');
                    }

                    // Set content
                    $('#editPostContent').val(postContent);

                    // Set image if exists
                    if (postData.image_path) {
                         $('#editImagePreview img').attr('src', 'http://localhost:8080' + postData.image_path);
                         $('#editImagePreview').show();
                         $('#removeImage').show();
                    }

                    // Store post ID and original image path
                    $('#editPostModal').data('post-id', postId);
                    $('#editPostModal').data('original-image', postData.image_path || '');
               } catch (error) {
                    console.error('Error setting edit data:', error);
                    alert('เกิดข้อผิดพลาดในการโหลดข้อมูล');
               }
          }

          // เพิ่มการกำหนดค่า Quill หลัง script ที่มีอยู่
          const toolbarOptions = [
               ['bold', 'italic', 'underline', 'strike'],
               [{ 'list': 'ordered' }, { 'list': 'bullet' }],
               [{ 'color': [] }, { 'background': [] }],
               ['clean']
          ];

          const quill = new Quill('#editor', {
               theme: 'snow',
               placeholder: 'คุณกำลังคิดอะไรอยู่?',
               modules: {
                    toolbar: toolbarOptions
               }
          });

          // เพิ่ม Quill instance สำหรับ edit modal
          const editQuill = new Quill('#editEditor', {
               theme: 'snow',
               modules: {
                    toolbar: toolbarOptions
               }
          });

          // เพิ่มฟังก์ชันรีเซ็ต Quill เมื่อโพสต์สำเร็จ
          function resetForm() {
               quill.setContents([]);
               $('#postForm')[0].reset();
               $('#image-preview-container').hide();
               $('#link-input-container').hide();
          }

          // เพิ่มฟังก์ชันรีเซ็ต edit form
          function resetEditForm() {
               editQuill.setContents([]);
               $('#editPostForm')[0].reset();
               $('#editImagePreview').hide();
               $('#removeImage').hide();
               $('#editPostLink').val('');
          }
     </script>
</body>

</html>
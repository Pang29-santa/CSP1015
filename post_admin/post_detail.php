<?php
if (!isset($_GET['id'])) {
     header('Location: post.php');
     exit;
}

$post_id = $_GET['id'];
$apiUrl = "http://localhost:8080/csP1015/project/post_admin/api.php?post_id=" . $post_id;
$response = file_get_contents($apiUrl);
$post = json_decode($response, true);

if (!$post || isset($post['message'])) {
     header('Location: post.php');
     exit;
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>รายละเอียดโพสต์ - ระบบจัดการ</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
     <link rel="stylesheet" href="../css/style_v.css">
     <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
     <style>
          .comment-section {
               max-height: 400px;
               overflow-y: auto;
          }

          .post-image {
               max-width: 100%;
               height: auto;
               border-radius: 8px;
          }

          .post-content {
               white-space: pre-wrap;
          }

          /* Quill editor styles */
          .ql-editor {
               min-height: 200px;
               background: white;
          }

          .ql-container {
               border-bottom-left-radius: 4px;
               border-bottom-right-radius: 4px;
          }

          .ql-toolbar {
               border-top-left-radius: 4px;
               border-top-right-radius: 4px;
               background: white;
          }

          /* Card styles */
          .card {
               box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
          }

          .card-header {
               background-color: #f8f9fa;
               border-bottom: 2px solid #e9ecef;
          }

          /* Content section styles */
          .content-section {
               background: #fff;
               padding: 20px;
               border-radius: 8px;
               margin-bottom: 20px;
          }

          /* Post content styles */
          .ql-editor {
               font-size: 16px;
               line-height: 1.6;
               padding: 20px !important;
               min-height: 200px;
               background: #f8f9fa;
               border-radius: 8px;
          }

          /* Stats section */
          .engagement-stats {
               background: #f8f9fa;
               padding: 15px;
               border-radius: 8px;
          }

          .engagement-stats i {
               font-size: 1.2em;
          }

          /* Comments section */
          .comment-section {
               max-height: 400px;
               overflow-y: auto;
               padding: 15px;
               background: #f8f9fa;
               border-radius: 8px;
          }

          .comment-card {
               background: white;
               padding: 15px;
               margin-bottom: 10px;
               border-radius: 6px;
               border: 1px solid #e9ecef;
          }

          /* Profile info */
          .profile-info {
               background: #f8f9fa;
               padding: 20px;
               border-radius: 8px;
               margin-bottom: 20px;
          }

          .profile-info p {
               margin-bottom: 0.5rem;
          }

          /* Back button */
          .back-btn {
               padding: 8px 20px;
               font-weight: 500;
          }

          .back-btn i {
               margin-right: 8px;
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
                         <a class="nav-link" href="../plant_diseases/P1.php">
                              <i class="bi bi-bug"></i> ข้อมูลโรคพืช
                         </a>
                         <a class="nav-link" href="../pests/PE1.php"><i class="bi-bug-fill"></i> ข้อมูลแมลงศัตรูพืช</a>
                         <a class="nav-link active" href="#"><i class="bi-newspaper"></i>
                              ข้อมูลโพส</a>
                         <a class="nav-link" href="../Users/U1.php"><i class="bi-person-circle"></i> ข้อมูลผู้ใช้งาน</a>
                    </nav>
               </div>

               <!-- Main Content -->
               <div class="col-md-10 p-4">
                    <div class="card">
                         <div class="card-header d-flex justify-content-between align-items-center">
                              <h4 class="mb-0">รายละเอียดโพสต์</h4>
                              <a href="post.php" class="btn btn-outline-secondary">
                                   <i class="bi bi-arrow-left"></i> กลับ
                              </a>
                         </div>
                         <div class="card-body p-4">
                              <!-- Profile Section -->
                              <div class="profile-info">
                                   <h5 class="mb-3">
                                        <i class="bi bi-person-circle me-2"></i>
                                        ข้อมูลผู้โพสต์
                                   </h5>
                                   <div class="row">
                                        <div class="col-md-6">
                                             <p><i class="bi bi-person me-2"></i><strong>ชื่อผู้ใช้:</strong>
                                                  <?php echo htmlspecialchars($post['username']); ?>
                                             </p>
                                             <p><i class="bi bi-card-text me-2"></i><strong>ชื่อ-นามสกุล:</strong>
                                                  <?php echo htmlspecialchars($post['full_name']); ?>
                                             </p>
                                        </div>
                                        <div class="col-md-6">
                                             <p><i class="bi bi-calendar me-2"></i><strong>วันที่โพสต์:</strong>
                                                  <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                                             </p>
                                        </div>
                                   </div>
                              </div>

                              <!-- Content Section -->
                              <div class="content-section">
                                   <h5 class="mb-3">
                                        <i class="bi bi-file-text me-2"></i>
                                        เนื้อหาโพสต์
                                   </h5>
                                   <div id="editor-container" class="mb-3"></div>
                                   <?php if (!empty($post['image_path'])): ?>
                                        <div class="text-center mt-3">
                                             <img src="<?php echo htmlspecialchars($post['image_path']); ?>"
                                                  class="post-image" alt="Post image">
                                        </div>
                                   <?php endif; ?>
                              </div>

                              <!-- Stats Section -->
                              <div class="engagement-stats">
                                   <h5 class="mb-3">
                                        <i class="bi bi-graph-up me-2"></i>
                                        การมีส่วนร่วม
                                   </h5>
                                   <div class="d-flex gap-4">
                                        <div>
                                             <i class="bi bi-heart-fill text-danger me-2"></i>
                                             <span class="fw-bold"><?php echo $post['likes_count']; ?></span> ไลค์
                                        </div>
                                        <div>
                                             <i class="bi bi-chat-fill text-primary me-2"></i>
                                             <span class="fw-bold"><?php echo $post['comments_count']; ?></span>
                                             ความคิดเห็น
                                        </div>
                                   </div>
                              </div>

                              <!-- Comments Section -->
                              <div class="comments-section">
                                   <h5>ความคิดเห็น</h5>
                                   <div class="comment-section">
                                        <?php if (isset($post['comments']) && !empty($post['comments'])): ?>
                                             <?php foreach ($post['comments'] as $comment): ?>
                                                  <div class="card mb-2 comment-card">
                                                       <div class="card-body">
                                                            <div class="d-flex justify-content-between">
                                                                 <h6 class="card-subtitle mb-2 text-muted">
                                                                      <?php echo htmlspecialchars($comment['username']); ?>
                                                                 </h6>
                                                                 <small class="text-muted">
                                                                      <?php echo $comment['formatted_date']; ?>
                                                                 </small>
                                                            </div>
                                                            <p class="card-text">
                                                                 <?php echo htmlspecialchars($comment['comment_text']); ?>
                                                            </p>
                                                       </div>
                                                  </div>
                                             <?php endforeach; ?>
                                        <?php else: ?>
                                             <p class="text-muted">ไม่มีความคิดเห็น</p>
                                        <?php endif; ?>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>

     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
     <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
     <script>
          // Configure Quill toolbar
          const toolbarOptions = [
               ['bold', 'italic', 'underline', 'strike'],
               ['blockquote', 'code-block'],
               [{ 'list': 'ordered' }, { 'list': 'bullet' }],
               [{ 'color': [] }, { 'background': [] }],
               ['link', 'clean']
          ];

          // Initialize Quill
          const quill = new Quill('#editor-container', {
               theme: 'snow',
               modules: {
                    toolbar: toolbarOptions
               },
               readOnly: true // Set to read-only mode
          });

          // Set content from PHP
          quill.root.innerHTML = `<?php echo addslashes($post['content']); ?>`;

          // Remove toolbar since we're in read-only mode
          document.querySelector('.ql-toolbar').style.display = 'none';

          // Apply custom styles to editor
          document.querySelector('.ql-container').style.border = 'none';
          document.querySelector('.ql-editor').style.backgroundColor = '#f8f9fa';
     </script>
</body>

</html>
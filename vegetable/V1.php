<?php
// ดึงข้อมูลจาก API
$apiUrl = "http://localhost:8080/csP1015/project/vegetable/api.php?page=" . ($_GET['page'] ?? 1);
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
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>ระบบตรวจสอบพืชสวนครัว</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
     <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
     <link rel="icon" href="img/plant.png" type="image/png">
     <link rel="stylesheet" href="../css/style_v.css">
</head>

<body>
     <div class="container-fluid">
          <div class="row">
               <div class="col-md-2 sidebar p-0">
                    <div class="p-4 text-center border-bottom">
                         <h5 class="text-white">Vegetables</h5>
                    </div>
                    <nav class="nav flex-column p-2">
                         <a class="nav-link active" href="#"><i class="bi bi-tree"></i> ข้อมูลผัก</a>
                         <a class="nav-link" href="../plant_diseases/P1.php">
                              <i class="bi bi-bug"></i> ข้อมูลโรคพืช
                         </a>
                         <a class="nav-link" href="../pests/PE1.php"><i class="bi bi-bug-fill"></i>
                              ข้อมูลแมลงศัตรูพืช</a>
                         <a class="nav-link" href="../post_admin/post.php"><i class="bi bi-newspaper"></i> ข้อมูลโพส</a>
                         <a class="nav-link" href="../Users/U1.php"><i class="bi bi-person-circle"></i>
                              ข้อมูลผู้ใช้งาน</a>
                    </nav>
               </div>
               <div class="col-md-10 p-4">
                    <div class="row mb-4 align-items-center">
                         <div class="col">
                              <h1 class="mb-3">ข้อมูลผัก</h1>
                         </div>
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
                                                  </div>
                                             </div>
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="./../login.php"><i
                                                       class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</a></li>
                                   </ul>
                              </div>
                         </div>
                    </div>

                    <div class="table-wrapper">
                         <div class="table-header">
                              <div class="search-container">
                                   <div class="search-box">
                                        <input type="text" id="searchInput" placeholder="ค้นหา..."
                                             onkeyup="searchTable()">
                                        <i class="bi bi-search"></i>
                                   </div>
                              </div>
                              <a href="V1_add.php">
                                   <button class="add-button">
                                        <i class="bi bi-plus"></i> เพิ่มข้อมูล
                                   </button>
                              </a>
                         </div>
                         <table class="table table-bordered" id="vegetableTable">
                              <thead>
                                   <tr>
                                        <th>No.</th>
                                        <th>ชื่อผัก (ภาษาไทย)</th>
                                        <th>ชื่อผัก (ภาษาอังกฤษ)</th>
                                        <th>รูปตัวอย่างผัก</th>
                                        <th>จัดการ</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   <?php foreach ($vegetables as $index => $row): ?>
                                        <tr>
                                             <td><?php echo ($index + 1) + ($page - 1) * 5; ?></td>
                                             <td><?php echo $row['thai_name']; ?></td>
                                             <td><?php echo $row['eng_name']; ?></td>
                                             <td>
                                                  <?php
                                                  // ตรวจสอบ id จาก API และแปลงเป็นรหัส 5 หลัก
                                                  if (isset($row['id']) && !empty($row['id'])) {
                                                       $vegetable_id = str_pad($row['id'], 5, '0', STR_PAD_LEFT);
                                                  } else {
                                                       $vegetable_id = "00000"; // ป้องกันข้อผิดพลาด
                                                  }

                                                  // สร้าง URL สำหรับรูปภาพจาก id
                                                  $imagePath = $row['image'] . "?v=" . time(); // เพิ่ม query string ด้วย time()
                                             
                                                  // แสดงรูปภาพ
                                                  echo '<img src="' . $imagePath . '" alt="' . $row['thai_name'] . '" width="500">';
                                                  ?>

                                             </td>
                                             <td>
                                                  <a href="V1_detail.php?id=<?php echo $row['id']; ?>"
                                                       class="btn btn-info btn-sm"><i class="bi bi-file-text"></i></a>
                                                  <a href="V1_edit.php?id=<?php echo $row['id']; ?>"
                                                       class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                                                  <button class="btn btn-danger btn-sm" onclick="deleteVegetable(this)"
                                                       data-id="<?php echo $row['id']; ?>">
                                                       <i class="bi bi-trash"></i>
                                                  </button>

                                             </td>
                                        </tr>
                                   <?php endforeach; ?>
                              </tbody>

                         </table>
                         <nav>
                              <ul class="pagination justify-content-center">
                                   <li class="page-item <?php if ($page <= 1)
                                        echo 'disabled'; ?>">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                   </li>
                                   <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php if ($i == $page)
                                             echo 'active'; ?>">
                                             <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                   <?php endfor; ?>
                                   <li class="page-item <?php if ($page >= $totalPages)
                                        echo 'disabled'; ?>">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                   </li>
                              </ul>
                         </nav>
                    </div>
               </div>
          </div>
          <script>
               function deleteVegetable(button) {
                    const vegetableId = button.getAttribute("data-id"); // รับค่า ID จากปุ่มที่กด

                    if (!vegetableId) {
                         alert("ไม่พบ ID ที่ต้องการลบ");
                         return;
                    }

                    if (!confirm(`คุณแน่ใจหรือไม่ว่าต้องการลบ ID ${vegetableId} ?`)) {
                         return; // ยกเลิกการลบถ้าผู้ใช้กดยกเลิก
                    }

                    fetch("http://localhost:8080/csP1015/project/vegetable/api.php", {
                         method: "DELETE",
                         headers: {
                              "Content-Type": "application/json"
                         },
                         body: JSON.stringify({ id: vegetableId }) // ส่งค่า ID ไปยัง API
                    })
                         .then(response => response.json())
                         .then(data => {
                              alert(data.message); // แสดงข้อความตอบกลับจาก API
                              location.reload(); // รีเฟรชหน้าเว็บหลังลบสำเร็จ
                         })
                         .catch(error => {
                              console.error("เกิดข้อผิดพลาด:", error);
                              alert("ไม่สามารถลบข้อมูลได้");
                         });
               }
          </script>
          <script>
               function searchTable() {
                    var input, filter, table, tr, td, i, txtValue;
                    input = document.getElementById("searchInput");
                    filter = input.value.toLowerCase();
                    table = document.getElementById("vegetableTable");
                    tr = table.getElementsByTagName("tr");

                    for (i = 1; i < tr.length; i++) {
                         td = tr[i].getElementsByTagName("td");
                         let found = false;

                         for (let j = 0; j < td.length; j++) {
                              if (td[j]) {
                                   txtValue = td[j].textContent || td[j].innerText;
                                   if (txtValue.toLowerCase().charAt(0) === filter.charAt(0)) {
                                        found = true;
                                        break;
                                   }
                              }
                         }

                         if (found) {
                              tr[i].style.display = "";
                         } else {
                              tr[i].style.display = "none";
                         }
                    }
               }
          </script>
     </div>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
<!DOCTYPE html>
<html lang="th">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>เข้าสู่ระบบ</title>
     <link rel="stylesheet" href="Register.css">
</head>

<body>
     <div class="login-container">
          <div class="login-form">
               <h2>เข้าสู่ระบบ</h2>
               <?php if (isset($_GET['message']) && $_GET['message'] === 'password_changed'): ?>
                    <div class="alert alert-success">
                         รหัสผ่านถูกเปลี่ยนแปลงแล้ว กรุณาเข้าสู่ระบบด้วยรหัสผ่านใหม่
                    </div>
               <?php endif; ?>
               <form action="http://localhost:8080/csP1015/project/login_api.php" method="post">
                    <input type="text" name="username" placeholder="ชื่อผู้ใช้" required>
                    <input type="password" name="password" placeholder="รหัสผ่าน" required>
                    <button type="submit">เข้าสู่ระบบ</button>
               </form>
               <p>ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></p>
          </div>
          <div class="login-image">
               <img src="image/206309-thumbnail.jpg" alt="User illustration" />
          </div>
     </div>
</body>

</html>
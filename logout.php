<?php
session_start();
session_destroy();
header("Location: login.php?message=password_changed");
exit;

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
if($_SESSION['admin'] == 1){
   header("Location: ../admin/home.php");
    exit();
}
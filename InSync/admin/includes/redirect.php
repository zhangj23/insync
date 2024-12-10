<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

elseif($_SESSION['admin'] == 0){
   header("Location: ../student/home.php");
    exit();
} 
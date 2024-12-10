<?php
include("../../includes/server.inc.php");
include("includes/redirect.php");
if(isset($_POST)){
   $stmt = $conn->prepare("SELECT clubs FROM customerdata WHERE id = ?");
   $stmt->bind_param("s", $_SESSION['user_id']);
   $stmt->execute();
   $result = $stmt->get_result();
   if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $userClubs = json_decode($row['clubs']);
   }
   array_push($userClubs, $_POST["id"]);
   $userClubs = json_encode($userClubs);
   $stmt = $conn->prepare("UPDATE customerdata SET clubs = ? WHERE id = ?");
   $stmt->bind_param("ss",$userClubs, $_SESSION['user_id']);
}
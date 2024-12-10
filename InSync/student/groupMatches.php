<?php
include("includes/redirect.php");
include("../includes/server.inc.php");
include("createGroups.php");

$currentUserId = $_SESSION['user_id'];

$generator = new GroupGenerator($conn);
$generator->generateGroups();
$generator->printGroups();
$generator->saveGroups();
?>
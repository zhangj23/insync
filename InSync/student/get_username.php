<?php
include("../includes/gzip.inc.php");
include("includes/redirect.php");
include("../includes/server.inc.php");


if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    
    // Use your existing database connection syntax
    $sql = "SELECT first_name FROM customerdata WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'username' => $row['first_name'] 
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'username' => 'User ' . $userId
        ]);
    }
    
    $stmt->close();
 } 
?>
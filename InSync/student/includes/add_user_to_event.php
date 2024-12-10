<?php
include '../../includes/server.inc.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = $_POST['eventId'];


$stmt = $conn->prepare("SELECT user_ids FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_ids = json_decode($row['user_ids'], true);


    if (!in_array($user_id, $user_ids)) {
        $user_ids[] = $user_id;
        $updated_user_ids = json_encode($user_ids);

        $update_stmt = $conn->prepare("UPDATE events SET user_ids = ? WHERE id = ?");
        $update_stmt->bind_param("si", $updated_user_ids, $event_id);

        if ($update_stmt->execute()) {
            echo json_encode(['message' => 'Event added to your personal calendar.']);
        } 
        else {
            echo json_encode(['message' => 'Failed to update event.']);
        }

        $update_stmt->close();
    } 

    else {
        echo json_encode(['message' => 'Event already in your personal calendar.']);
    }
} 

else {
    echo json_encode(['message' => 'Event not found.']);
}

$stmt->close();
$conn->close();
?>

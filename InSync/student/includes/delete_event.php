<?php
include '../../includes/server.inc.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "User not logged in!";
    exit();
}

if (!isset($_POST['eventId'])) {
    echo "Event ID not provided!";
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = $_POST['eventId'];

$stmt = $conn->prepare("SELECT creator, user_ids FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $creator_id = $row['creator'];
    $user_ids = json_decode($row['user_ids'], true);

    if ($user_id == $creator_id) {
        $delete_stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $delete_stmt->bind_param("i", $event_id);
        $delete_stmt->execute();

        if ($delete_stmt->affected_rows > 0) {
            echo "Event deleted successfully!";
        } else {
            echo "Error deleting event.";
        }

        $delete_stmt->close();
    } else {
        // User is not the creator; just remove them from the user_ids array
        if (($key = array_search($user_id, $user_ids)) !== false) {
            unset($user_ids[$key]);
        }

        // Update the event's user_ids in the database
        $new_user_ids = json_encode(array_values($user_ids));
        $update_stmt = $conn->prepare("UPDATE events SET user_ids = ? WHERE id = ?");
        $update_stmt->bind_param("si", $new_user_ids, $event_id);
        $update_stmt->execute();

        if ($update_stmt->affected_rows > 0) {
            echo "User removed from event successfully!";
        } else {
            echo "Error updating event.";
        }

        $update_stmt->close();
    }
} else {
    echo "Event not found!";
}

$stmt->close();
$conn->close();
?>

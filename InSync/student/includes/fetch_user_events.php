<?php
include '../../includes/server.inc.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM events WHERE JSON_CONTAINS(user_ids, JSON_ARRAY(?))");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => $row['id'],
        'title' => $row['name'],
        'start' => $row['date'] . 'T' . $row['start_time'],
        'end' => $row['date'] . 'T' . $row['end_time'],
        'location' => $row['location'],
    ];
}

echo json_encode($events);

$stmt->close();
$conn->close();
?>

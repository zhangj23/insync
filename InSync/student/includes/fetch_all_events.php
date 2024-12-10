<?php
include '../../includes/server.inc.php';

$stmt = $conn->prepare("SELECT * FROM events WHERE clubid IS NOT NULL");
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

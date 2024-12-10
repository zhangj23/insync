<?php
    // Fetch previously created events
    $query = $conn->prepare("SELECT name, date, start_time, end_time FROM events WHERE creator = ? ORDER BY date DESC");
    $query->bind_param("i", $_SESSION['user_id']);
    $query->execute();
    $result = $query->get_result();
    $count = 4;

    if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if($count >0){
                echo "<div class='event-item'>";
                echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                echo "<p>Date: " . htmlspecialchars($row['date']) . "</p>";
                echo "<p>Start Time: " . htmlspecialchars($row['start_time']) . "</p>";
                echo "<p>End Time: " . htmlspecialchars($row['end_time']) . "</p>";
                echo "</div>";
                $count = $count-1;
            }
            
        }
    } else {
        echo "<p>No events found.</p>";
    }
    $conn->close();
?>
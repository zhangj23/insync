<?php 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars(trim($_POST['eventTitle']));
    $date = htmlspecialchars(trim($_POST['eventDate']));
    $description = htmlspecialchars(trim($_POST['eventDescription']));
    $location = htmlspecialchars(trim($_POST['eventLoc'] ?? ''));
    $startTime = htmlspecialchars(trim($_POST['startTime']));
    $endTime = htmlspecialchars(trim($_POST['endTime']));
    $selectedItems = isset($_POST['item']) ? $_POST['item'] : [];
    $eventEmails = isset($_POST['eventEmails']) ? trim($_POST['eventEmails']) : '';

    $updatedselectedItems = json_encode($selectedItems);

    // Initialize user_ids array and add the current user ID
    $user_ids = [];
    $current_user_id = $_SESSION['user_id']; // Assuming user ID is stored in session
    $user_ids[] = $current_user_id;

    // Convert email string to an array
    $emailArray = array_map('trim', explode(',', $eventEmails));
    $emailArray = array_filter($emailArray, function ($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL); // Validate emails
    });

    // Fetch user IDs for valid emails
    if (!empty($emailArray)) {
        $placeholders = implode(',', array_fill(0, count($emailArray), '?'));
        $query = "SELECT id FROM customerdata WHERE email IN ($placeholders)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(str_repeat('s', count($emailArray)), ...$emailArray);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $user_ids[] = $row['id'];
        }
        $stmt->close();
    }

    // Convert user_ids to JSON
    $user_ids_json = json_encode(array_unique($user_ids)); // Ensure IDs are unique

    // Determine club ID if applicable
    if ($_SESSION['admin'] == 1) {
        $clubid = $_SESSION['club_id'];
    } else {
        $clubid = null;
    }

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("INSERT INTO events (name, location, tags, date, user_ids, clubid, start_time, end_time, creator) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $title, $location, $updatedselectedItems, $date, $user_ids_json, $clubid, $startTime, $endTime, $current_user_id);

    if (!$stmt->execute()) {
        echo "<div class='messages'><h4>Error: " . $stmt->error . "</h4></div>";
    }

    $stmt->close();
}



?>
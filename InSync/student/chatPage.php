<?php
include("../includes/gzip.inc.php");
include("includes/redirect.php");
include("../includes/server.inc.php");
require_once("getMatches.php");

$currentUserId = $_SESSION['user_id'];

$matches = getMatches();

$groupQuery = "SELECT DISTINCT group_number 
               FROM user_groups 
               WHERE user_id = ?";
$stmt = $conn->prepare($groupQuery);
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$groupResult = $stmt->get_result();

$userGroups = [];
while ($group = $groupResult->fetch_assoc()) {
    // Fetch group members
    $membersQuery = "SELECT cd.id, cd.first_name, cd.last_name 
                     FROM user_groups ug
                     JOIN customerdata cd ON ug.user_id = cd.id
                     WHERE ug.group_number = ? AND cd.id != ?";
    $membersStmt = $conn->prepare($membersQuery);
    $membersStmt->bind_param("ii", $group['group_number'], $currentUserId);
    $membersStmt->execute();
    $membersResult = $membersStmt->get_result();
    
    $groupMembers = [];
    while ($member = $membersResult->fetch_assoc()) {
        $groupMembers[] = $member;
    }
    
    $userGroups[] = [
        'group_number' => $group['group_number'],
        'members' => $groupMembers
    ];
}
?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chat - InSync</title>   
    <link rel="stylesheet" href="resources/chat.css" />
  </head>
  <body>
    <div class="sections" data-user1="<?php echo $currentUserId; ?>">
      <!-- Navigation Section -->
      <section>
         <?php include("includes/nav.inc.php"); ?>
      </section>
      
      <section>
          <div id="user-list">
              <h3>Select a User to Chat With:</h3>
              <?php
                  if (!empty($matches)) {
                      foreach ($matches as $match) {
                          echo '<button class="user-btn" data-user-id="' . $match['id'] . '">' . 
                              htmlspecialchars($match['first_name'] . ' ' . $match['last_name']) . 
                              ' (' . round($match['match_percentage']) . '% match)</button>';
                      }
                  } else {
                      echo "<p>No matches found.</p>";
                  }
              ?>
              <h3>Your Groups:</h3>
            <?php
                if (!empty($userGroups)) {
                    foreach ($userGroups as $group) {
                        $memberNames = array_map(function($member) {
                            return htmlspecialchars($member['first_name'] . ' ' . $member['last_name']);
                        }, $group['members']);
                        
                        $displayMembers = implode(', ',($memberNames));
                        
                        echo '<button class="group-btn" data-group-id="' . $group['group_number'] . '">' . 
                            'Group ' . $group['group_number'] . ': ' . $displayMembers . 
                            '</button>';
                    }
                } else {
                    echo "<p>No groups found.</p>";
                }
            ?>
          </div>
      </section>

      <!-- Chat Section -->
      <section id = "chat-box">
        <div id="chat">
            <h3>Chat</h3>
            <ul id="messages"></ul>
            <form id="send-message">
              <input id="chat-txt" type="text" placeholder="Type your message here" />
              <button id="chat-btn" type="submit">Submit</button>
            </form>
          </div>
        </div>
      </section>

    <!-- Firebase and JavaScript Files -->
    <script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-firestore.js"></script>
    <script src="scripts/chat.js"></script>
  </body>
</html>


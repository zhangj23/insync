<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Edit Clubs - InSync</title>
   <link rel="stylesheet" href="resources/editClub.css" />


</head>

<body>
   <div class="sections">
      <section>
         <?php
         include("../includes/gzip.inc.php");
         ini_set('display_errors', 1);
         ini_set('display_startup_errors', 1);
         error_reporting(E_ALL);
         include("includes/nav.inc.php");
         include("includes/redirect.php");
         include("../includes/server.inc.php");
         if ($_SESSION['user_id']) {
            // Step 1: Get the current user's tags
            $user_query = "SELECT tags FROM customerdata WHERE id = ?";
            $stmt = $conn->prepare($user_query);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $user_result = $stmt->get_result();
            $user_data = $user_result->fetch_assoc();
            $user_tags = json_decode($user_data['tags'], true);

            // Step 2: Get all clubs from the database
            $clubs_query = "SELECT id, name, description, tags FROM clubs";
            $stmt = $conn->prepare($clubs_query);
            $stmt->execute();
            $result = $stmt->get_result();

            // Step 3: Match clubs based on tags
            $clubMatches = array();
            while ($row = $result->fetch_assoc()) {
               $club_tags = json_decode($row['tags'], true);

               if (!empty($user_tags) && !empty($club_tags)) {
                  // Find common tags
                  $common_tags = array_values(array_intersect($user_tags, $club_tags));
                  $match_count = count($common_tags);

                  // Calculate match percentage
                  $min_tag_count = min(count($user_tags), count($club_tags));
                  if ($min_tag_count > 0) {
                     $match_percentage = ($match_count / $min_tag_count) * 100;

                     // Only include matches with at least 50% tag overlap
                     if ($match_percentage >= 20) {
                        $clubMatches[] = array(
                           'id' => $row['id'],
                           'name' => $row['name'],
                           'description' => $row['description'],
                           'common_tags' => $common_tags,
                           'match_percentage' => $match_percentage,
                           'match_count' => $match_count
                        );
                     }
                  }
               }
            }

            // Step 4: Sort matches by match percentage
            usort($clubMatches, function ($a, $b) {
               return $b['match_percentage'] <=> $a['match_percentage'];
            });

            // Limit matches to top 5
            $clubMatches = array_slice($clubMatches, 0, 5);
            $_SESSION['clubMatches'] = $clubMatches;
         }

         //print_r($_SESSION['matches']);

         if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = (int)htmlspecialchars($_POST["id"]);
            $stmt = $conn->prepare("SELECT clubs FROM customerdata WHERE id = ?");
            $stmt->bind_param("s", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
               $row = $result->fetch_assoc();
               $userClubs = json_decode($row['clubs']);
            }
            if (in_array($id, $userClubs)) {
               header("Location: " . $_SERVER['PHP_SELF']);
               exit;
            }
            array_push($userClubs, $id);
            $userClubs = json_encode($userClubs);
            echo $userClubs;
            $stmt = $conn->prepare("UPDATE customerdata SET clubs = ? WHERE id = ?");
            $stmt->bind_param("ss", $userClubs, $_SESSION['user_id']);
            $stmt->execute();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
         }

         if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
            $deleteData = json_decode(file_get_contents("php://input"), true);
            $id = $deleteData['id'] ?? null;
            echo $id;

            $stmt = $conn->prepare("SELECT clubs FROM customerdata WHERE id = ?");
            $stmt->bind_param("s", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
               $row = $result->fetch_assoc();
               $userClubs = json_decode($row['clubs']);
            }

            $index = array_search($id, $userClubs);
            array_splice($userClubs, $index, 1);
            $userClubs = json_encode($userClubs);
            echo $userClubs;
            $stmt = $conn->prepare("UPDATE customerdata SET clubs = ? WHERE id = ?");
            $stmt->bind_param("ss", $userClubs, $_SESSION['user_id']);
            $stmt->execute();
         }

         $clubData = [];
         $stmt = $conn->prepare("SELECT clubs FROM customerdata WHERE id = ?");
         $stmt->bind_param("s", $_SESSION['user_id']);
         $stmt->execute();
         $result = $stmt->get_result();
         if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userClubs = json_decode($row['clubs']);
         }
         $stmt = $conn->prepare("SELECT * from clubs");
         $stmt->execute();
         $result = $stmt->get_result();
         while ($row = $result->fetch_assoc()) {
            $clubData[] = $row; // Decode JSON and store
         }
         ?>
      </section>

      <main>

         <div class="container">
            <div class="my-clubs-wrapper">
               <h2>Your Clubs</h2>
               <div class="my-clubs">
                  <?php
                  function isClub($var)
                  {
                     return in_array($var["id"], $GLOBALS['userClubs']);
                  }
                  $userClubsArray = array_filter($clubData, "isClub");

                  foreach ($userClubsArray as $club) {

                     echo '<div class="club-item-wrapper">
                              <div class="club-item">
                                 <a href="club.php?id='.$club["id"].'">' . $club["name"] . '</a>
                                 <img class="delete-club" src="../public/delete.png" alt="delete club" data-id="' . $club["id"] . '">
                              </div>
                           </div>';
                  }

                  ?>
               </div>
            </div>
            <?php if (!isset($_SESSION['clubMatches'])) {
               $_SESSION['clubMatches'] = [];
            }
            if (!empty($_SESSION['clubMatches'])) ?>
            <div class="seperator"></div>
            <div class="add-clubs-wrapper">
               <h2>Recommended Clubs</h2>
               <div class="recommended-clubs-container">
                  <?php
                  foreach ($_SESSION['clubMatches'] as $match) {
                     $image = "add.png";
                     $class = "add";
                     if (in_array((int)$match["id"], $userClubs)) {
                        $class = "done";
                        $image = "done.webp";
                     }
                     echo '<div class="club-item-wrapper">
                           <div class="club-item">
                              <a href="club.php?id=' . $match["id"] . '">' . htmlspecialchars($match["name"]) . '</a>
                              <img src="../public/' . $image . '" alt="add club" class="' . $class . '-club" data-id="' . $match["id"] . '">
                           </div>
                        </div>';
                  }
                  ?>
               </div>
               <h2>Add Clubs</h2>
               <div class="add-clubs-container">
                  <?php

                  foreach ($clubData as $row) {
                     $image = "add.png";
                     $class = "add";
                     if (in_array((int)$row["id"], $userClubs)) {
                        $class = "done";
                        $image = "done.webp";
                     }
                     echo '<div class="club-item-wrapper">
                        <div class="club-item">
                           <a href="club.php?id=' . $row["id"] . '">' . $row["name"] . '</a>
                           <img src="../public/' . $image . '" alt="add club" class="' . $class . '-club" data-id="' . $row["id"] . '" >
                        </div>
                     </div>';
                  }
                  ?>
               </div>
            </div>
         </div>
      </main>
   </div>
   <script src="scripts/editClubs.js"></script>
</body>

</html>
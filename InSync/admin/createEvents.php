<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Home - InSync</title>
   <!-- <link rel="stylesheet" href="resources/clubInfo.css" /> -->
   <link rel="stylesheet" href="../student/resources/event.css" />
</head>

<body>
   <div class="sections">
      <section>
         <?php 
         include("../includes/gzip.inc.php");
            include ("../includes/server.inc.php");
            include("includes/nav.inc.php");
            include("includes/redirect.php");
            if($_SESSION['club_filled'] == 0){
               header("Location: clubInfo.php");
               exit();
            }
         ?>
      </section>

      <main>
         <!-- Form Section -->
         <?php
        include ("../includes/createEvent.php");
    ?>
         <!-- <header>
            <h2>Create a New Event</h2>
        </header><br> -->
         <?php
         if (!empty($errors)) {
            ?>
         <div id="messages">
            <?php
            echo $errors;
            ?>
         </div>
         <?php
         }
            // Process form submission
            // if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item'])) {
                // $selectedItems = $_POST['item'];
                // echo "<div class='selection-results'>";
                // echo "<h3>Selected Items:</h3>";
                // echo "<ul>";

                // foreach ($selectedItems as $item) {
                //     echo "<li>" . htmlspecialchars($item) . "</li>";
                // }

                // echo "</ul>";
                // echo "<p>Total items selected: " . count($selectedItems) . "</p>";
                // echo "</div>";

                // $updatedselectedItems = json_encode($selectedItems);
                // $stmt = $conn->prepare("UPDATE `events` SET `tags` = ? WHERE `events`.`id` = ?;");
                // $stmt->bind_param("si", $updatedselectedItems, $_SESSION['user_id']);
                // $stmt->execute();
            // }
         ?>
         <div class="contentWrapper">
            <div class="formSectionWrapper">
               <h2>Create a New Event</h2>
               <form class="formSection" id="event-form" action="#" method="POST">
                  <div class="form-item">
                     <label for="eventTitle">Event Title:</label>
                     <input type="text" id="eventTitle" name="eventTitle" required><br><br>

                     <label for="eventDate">Event Date:</label>
                     <input type="date" id="eventDate" name="eventDate" required><br><br>

                     <label for="eventLoc">Event Location:</label>
                     <input type="text" id="eventLoc" name="eventLoc" required><br><br>

                     <label for="eventDescription">Event Description:</label><br>
                     <textarea id="eventDescription" name="eventDescription" rows="4" cols="30"
                        required></textarea><br><br>
                     <label>Tags: </label>
                     <div class="select-container">
                        <div id="selectedItems" class="selected-items"></div><br>
                        <div class="selected-value" id="selectedValue">Select tags</div>
                        <input type="text" class="search-input" id="searchInput" placeholder="Search tags..." />
                        <div class="custom-select" id="customSelect">
                           <?php
                        // Initialize the $selectedTags variable as an empty array
                        $selectedTags = [];

                        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item'])) {
                            $selectedTags = $_POST['item']; // Populate selectedTags with user-selected items
                        }
                        $selectedTags = [];

                        // Then get all available tags
                        $stmt = $conn->prepare("SELECT * FROM tagtable");
                        $stmt->execute();
                        $sql = $stmt->get_result();

                        while ($row = $sql->fetch_assoc()) {
                            // Check if the tag is in the selectedTags array
                            $isSelected = in_array($row["tag"], $selectedTags) ? ' selected' : '';
                            ?>
                           <div class="select-option<?php echo $isSelected ?>"
                              data-value="<?php echo htmlspecialchars($row["tag"]) ?>">
                              <?php echo htmlspecialchars($row["tag"]) ?>
                           </div>
                           <?php
                        }
                        ?>

                        </div>
                        <select name="item[]" id="originalSelect" class="hidden" multiple>
                           <?php
                        // Reset the result set
                        $stmt->execute();
                        $sql = $stmt->get_result();
                        while ($row = $sql->fetch_assoc()) {
                            $isSelected = in_array($row["tag"], $selectedTags) ? ' selected="selected"' : '';
                        ?>
                           <option value="<?php echo htmlspecialchars($row["tag"]) ?>" <?php echo $isSelected ?>>
                              <?php echo htmlspecialchars($row["tag"]) ?>
                           </option>
                           <?php
                        }
                        ?>
                        </select>
                     </div><br>

                     <label for="startTime">Start Time:</label>
                     <input type="time" id="startTime" name="startTime" required><br><br>

                     <label for="endTime">End Time:</label>
                     <input type="time" id="endTime" name="endTime" required><br><br>

                     <input type="submit" value="Create Event">
                  </div>
               </form>
            </div>

            <!-- Events Section -->

            <div class="eventSectionWrapper">
               <h2>Previously Created Events</h2>
               <div class="formSection">
                  <div class="form-item">
                     <?php
                    include ("../includes/eventList.php");
                    ?>
                  </div>
               </div>
            </div>
         </div>
   </div>
   </main>

   </div>
   <script src="../student/scripts/search.js"></script>
   <script>
   document.addEventListener('DOMContentLoaded', function() {
      // Select all input fields and textarea
      const fields = document.querySelectorAll('input, textarea');

      // Add focus and blur event listeners
      fields.forEach(field => {
         field.addEventListener('focus', function() {
            this.classList.add('active'); // Add the 'active' class on focus
         });

         field.addEventListener('blur', function() {
            this.classList.remove('active'); // Remove the 'active' class on blur
         });
      });
   });
   </script>
</body>

</html>
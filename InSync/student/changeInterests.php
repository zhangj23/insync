<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Change Interests - InSync</title>
   <link rel="stylesheet" href="resources/interest.css" />
</head>

<body>

    <div class="sections">
        <section class ="nav_section">
            <?php
            include("../includes/gzip.inc.php");
            include("includes/nav.inc.php");
            include("includes/redirect.php");
            include("../includes/server.inc.php");
            ?>
      </section>
      <main>
         <header>
            <h1>Change Your Interests</h1>
         </header>
         <div class="formSectionWrapper">
            <div class="formSection">
               <?php
            // Process form submission
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item'])) {
                $selectedItems = $_POST['item'];
                echo "<div class='selection-results'>";
                echo "<h3>Selected Items:</h3>";
                echo "<ul>";

                foreach ($selectedItems as $item) {
                    echo "<li>" . htmlspecialchars($item) . "</li>";
                }

                echo "</ul>";
                echo "<p>Total items selected: " . count($selectedItems) . "</p>";
                echo "</div>";

                $updatedselectedItems = json_encode($selectedItems);
                $stmt = $conn->prepare("UPDATE `customerdata` SET `tags` = ? WHERE `customerdata`.`id` = ?;");
                $stmt->bind_param("si", $updatedselectedItems, $_SESSION['user_id']);
                $stmt->execute();
            }
            ?>

               <form class="form-item" name="form1" action="" method="post">
                  <label>Tags</label>
                  <div class="select-container">
                     <div id="selectedItems" class="selected-items"></div><br>
                     <div class="selected-value" id="selectedValue">Select tags</div>
                     <input type="text" class="search-input" id="searchInput" placeholder="Search tags..." />
                     <div class="custom-select" id="customSelect">
                        <?php
                        // First, get the user's current tags
                        $userStmt = $conn->prepare("SELECT tags FROM customerdata WHERE id = ?");
                        $userStmt->bind_param("i", $_SESSION['user_id']);
                        $userStmt->execute();
                        $userResult = $userStmt->get_result();
                        $userRow = $userResult->fetch_assoc();
                        $selectedTags = json_decode($userRow['tags'], true) ?? [];

                        // Then get all available tags
                        $stmt = $conn->prepare("select * from tagtable");
                        $stmt->execute();
                        $sql = $stmt->get_result();
                        while ($row = $sql->fetch_assoc()) {
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
                  </div>
                  <br>
                  <input type="submit" id="btn btn-secondary" value="Submit">
                  <div id="submissionResult"></div>
               </form>
      </main>
   </div>

   <script src="scripts/search.js"></script>

</body>

</html>
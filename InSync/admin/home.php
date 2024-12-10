<!DOCTYPE html>
<html lang="en">


<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Home - InSync</title>
   <link rel="stylesheet" href="resources/home.css" />
</head>

<body>
   <div class="sections">
      <section>
         <?php 
         include("../includes/gzip.inc.php");
            include("../includes/server.inc.php");
            include("includes/nav.inc.php");
            include("includes/redirect.php");
            if($_SESSION['club_filled'] == 0){
               header("Location: clubInfo.php");
               exit();
            }
            
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

               $name = htmlspecialchars(trim($_POST["name"]));
               $imageFile = $_FILES["image"]["tmp_name"];
               if($imageFile){
                  $imageData = file_get_contents($imageFile);
               } else{
                  $imageData = null;
               }
               
               $description = htmlspecialchars(trim($_POST["description"]));
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

               $tags = json_encode($selectedItems);
               if($imageData){
                  $stmt = $conn->prepare("UPDATE `clubs` SET name = ?, image = ?, description = ?, tags = ? WHERE `clubs`.`userid` = ?;");
                  $stmt->bind_param("sbsss", $name, $null, $description, $tags, $_SESSION["user_id"]);
                  $stmt->send_long_data(1, $imageData);
               } else{
                  $stmt = $conn->prepare("UPDATE `clubs` SET name = ?,  description = ?, tags = ? WHERE `clubs`.`userid` = ?;");
                  $stmt->bind_param("ssss", $name,  $description, $tags, $_SESSION["user_id"]);
               }
               
               if ($stmt->execute()) {
                  header("Location: home.php");
                  exit();
               } else {
                     echo "<div class='messages'><h4>Error: " . $stmt->error . "</h4></div>";
               }
               $stmt->close();
            } 

            $stmt = $conn->prepare("SELECT * FROM clubs WHERE userid = ?");
            $stmt->bind_param("s", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $club = $result->fetch_assoc();
            $imageSrc = "data:" . 'image/jpeg' . ";base64," . base64_encode($club["image"]);
         ?>
      </section>

      <main>
         <div class="left">
            <div class="main-wrapper">
               <div class="header">
                  <h1>Club Info</h1>
                  <button class="edit-btn"><img src="../public/edit.png" alt="Edit button"></button>
                  <button class="cancel-btn hidden"><img src="../public/cancel.png" alt="Edit button"></button>
               </div>

               <form action="" method="POST" enctype="multipart/form-data">
                  <div class="normal-fields">
                     <fieldset>
                        <label for="name">Club Name</label>
                        <?php echo "<p>".$club["name"]."</p>"?>
                     </fieldset>
                     <hr>
                     <fieldset>
                        <label for="description">Description</label>
                        <?php echo "<p>".$club["description"]."</p>"?>
                     </fieldset>
                     <hr>

                  </div>

                  <div class="edit-fields hidden">
                     <fieldset>
                        <label for="name">Club Name</label>
                        <input id="name" name="name" type="text" value="<?php echo $club["name"]?>"
                           placeholder="My club name">
                     </fieldset>
                     <hr>
                     <fieldset>
                        <label for="image">Image Upload</label>
                        <input id="image" name="image" accept="image/*" type="file">
                     </fieldset>
                     <hr>
                     <fieldset>
                        <label for="description">Description</label>
                        <textarea name="description" id="description"
                           placeholder="Type description here"><?php echo $club["description"]?></textarea>
                     </fieldset>
                     <hr>
                  </div>

                  <fieldset>
                     <label for="tags">Tags</label>

                     <div class="select-container">
                        <div id="selectedItems" class="selected-items"></div><br>
                        <div class="selected-value hidden" id="selectedValue">Select items</div>
                        <input type="text" class="search-input " id="searchInput" placeholder="Search items..." />
                        <div class="custom-select" id="customSelect">
                           <?php
                        // First, get the club's current tags
                        $userStmt = $conn->prepare("SELECT tags FROM clubs WHERE id = ?");
                        $userStmt->bind_param("i", $club["id"]);
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
                  </fieldset>

                  <button class="hidden" id="submit" type="text" type="submit">Submit Changes</button>
               </form>
            </div>

         </div>
         <div class="right">
            <h2>Club Image</h2>
            <img src="<?php echo $imageSrc; ?>" alt="Image" id="output">
         </div>
      </main>
   </div>

   <script src="scripts/home.js"></script>

</body>

</html>
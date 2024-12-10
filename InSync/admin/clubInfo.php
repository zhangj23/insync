<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Home - InSync</title>
   <link rel="stylesheet" href="resources/clubInfo.css" />
</head>

<body>
   <div class="sections">
      <section>
         <?php 
         include("../includes/gzip.inc.php");
            include("../includes/server.inc.php");
            include("includes/redirect.php");
            

            $havePost = false; 

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
               $stmt = $conn->prepare("INSERT INTO clubs (name, image, description, tags, userid) VALUES (?, ?, ?, ?, ?)");
               $stmt->bind_param("sbsss", $name, $null, $description, $tags, $_SESSION["user_id"]);
               $stmt->send_long_data(1, $imageData);
               if ($stmt->execute()) {
                  echo "<div class='messages'><h4>Account created successfully!</h4></div>";
                  $_SESSION['club_filled'] = 1;
                  header("Location: home.php");
                  exit();
               } else {
                     echo "<div class='messages'><h4>Error: " . $stmt->error . "</h4></div>";
               }
               $stmt->close();
            }
            if($_SESSION['club_filled'] == 1){
               header("Location: home.php");
               exit();
            }
         ?>
      </section>

      <main>
         <div class="left">
            <div class="main-wrapper">
               <h1>Enter Club Info</h1>

               <form action="" method="POST" enctype="multipart/form-data">
                  <fieldset>
                     <label for="name">Club Name</label>
                     <input id="name" name="name" type="text" placeholder="My club name">
                  </fieldset>
                  <hr>
                  <fieldset>
                     <label for="image">Image Upload</label>
                     <input id="image" name="image" accept="image/*" type="file">
                  </fieldset>
                  <hr>
                  <fieldset>
                     <label for="description">Description</label>
                     <textarea name="description" id="description" placeholder="Type description here"></textarea>
                  </fieldset>
                  <hr>
                  <fieldset>
                     <label for="tag">Tag</label>
                     <div class="select-container">
                        <div id="selectedItems" class="selected-items"></div><br>
                        <div class="selected-value" id="selectedValue">Select items</div>
                        <input type="text" class="search-input" id="searchInput" placeholder="Search items..." />
                        <div class="custom-select" id="customSelect">
                           <?php
                        $selectedTags =  [];

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

                  <button id="submit" type="text" type="submit">Add Club</button>
               </form>
            </div>

         </div>
         <div class="right">
            <a id="logout" href="../auth/logout.php">Logout</a>
            <h2>Image Preview</h2>
            <img src="https://placehold.co/600x400" alt="Image Preview" id="output">
         </div>
      </main>
   </div>
   <script src="scripts/add.js"></script>
</body>

</html>
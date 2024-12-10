<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>
   <link rel="stylesheet" href="resources/club.css">
</head>

<body>
   <div class="sections">
      <section>
         <?php 
         include("../includes/gzip.inc.php");
         include("includes/nav.inc.php");
            include("includes/redirect.php");
            include("../includes/server.inc.php");


            if($_SERVER["REQUEST_METHOD"] === "GET"){
               $stmt = $conn->prepare("SELECT * FROM clubs WHERE id = ?");
               $stmt->bind_param("s", $_GET["id"]);
               $stmt->execute();
               $club_result = $stmt->get_result();
               $club = $club_result->fetch_assoc();
               $imageSrc = "data:" . 'image/jpeg' . ";base64," . base64_encode($club["image"]);
               $userStmt = $conn->prepare("SELECT tags FROM clubs WHERE id = ?");
               $userStmt->bind_param("i", $club["id"]);
               $userStmt->execute();
               $userResult = $userStmt->get_result();
               $userRow = $userResult->fetch_assoc();
               $selectedTags = json_decode($userRow['tags'], true) ?? [];
            }
            ?>
      </section>

      <main>
         <div class="club-contents">
            <div class="club-image">
               <img id="image" src=<?php echo $imageSrc;?> alt="club image">
            </div>
            <div class="vertical-line"></div>
            <div class="club-information-wrapper">
               <div class="club-information">
                  <div>
                     <h1><?php echo $club["name"];?></h1>

                     <!-- <h2>Description</h2> -->
                     <p><?php echo $club["description"]?></p>
                     <hr>
                  </div>

                  <h2>Tags</h2>
                  <div class="select-container">
                     <div id="selectedItems" class="selected-items">
                        <?php 
                     foreach($selectedTags as $tag){
                        echo '<div class="selected-tag"
                                 data-value="'.htmlspecialchars($tag).'">
                                 '.htmlspecialchars($tag).'
                              </div>';
                     }
            ?></div>
                  </div>

               </div>
               <br>
            </div>
         </div>



   </div>
   </main>
   </div>
</body>

</html>
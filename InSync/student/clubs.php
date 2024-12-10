<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Clubs - InSync</title>
   <link rel="stylesheet" href="resources/home.css" />

</head>

<body>

   <div class="sections">
      <section>
         <?php 
         include("../includes/gzip.inc.php");
         include("includes/nav.inc.php");
            include("includes/redirect.php")
            ?>
      </section>

      <main>
         <header>
            <h1>Clubs</h1>
         </header>
         <ul>
            <li>Club 1</li>
            <li>Club 2</li>
            <li>Club 3</li>
         </ul>
      </main>
   </div>

   <button class="homePageButton" onclick="window.location.href='home.php'">Home Page</button>

</body>

</html>
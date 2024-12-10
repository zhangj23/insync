<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Event Details - InSync</title>
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
            <h1>Event Title</h1>
            <p>Date: 10/10/2024</p>
         </header>
         <section>
            <h2>Description</h2>
            <p>Details about the event...</p>
         </section>
         <button class="eventPageButton" onclick="window.location.href='events.php'"> Events Page</button>
         <button class="homePageButton" onclick="window.location.href='home.php'">Home Page</button>
      </main>
   </div>

</body>

</html>
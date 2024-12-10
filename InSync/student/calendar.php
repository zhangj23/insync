<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Calendar - InSync</title>

   <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
   <script defer src="scripts/all_events_calendar.js"></script>
   <link rel="stylesheet" href="resources/personalCalendar.css" />
   <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

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

      <div id="user-data" data-user-id="<?php echo $_SESSION['user_id']; ?>"></div>

      <section id="calendar-section">

         <div id="calendar"></div>


         <div class="event-hover-class"></div>

      </section>


   </div>



</body>

</html>
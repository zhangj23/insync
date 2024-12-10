<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - InSync</title>
    <!-- <link rel="stylesheet" href="resources/studentStyle.css" /> -->
    <link rel="stylesheet" href="resources/home.css" />


</head>

<body>
    <div class="sections">
        <section>
            <?php include("includes/nav.inc.php");
            include("includes/redirect.php")
            ?>
        </section>

        <main class="events-section">
            <header>
                <h1>Upcoming Events</h1>
            </header>
            <ul>
                <li><a href="viewSpecificEvents.php">Event 1</a></li>
                <li><a href="viewSpecificEvents.php">Event 2</a></li>
                <li><a href="viewSpecificEvents.php">Event 3</a></li>
            </ul>
        </main>
    </div>

</body>

</html>
<img id="open-nav" src="../public/hamburger.png" alt="Toggle Navbar">
<nav id="nav-bar">
   <ul class="nav-links">
      <li>
         <a href="home.php" class="logo-link">
            <img src="../public/logostar.png" alt="InSync Logo" class="logo-image" />
            InSync
         </a>
      </li>
      <li>
         <button class="nav-button" id="event-button" data-open="false">
            <img src="../public/schedule.png" alt="Events page " />Events<img class="dropdown-icon"
               src="../public/arrow-drop-down.svg" alt="Dropdown Icon" /></button>
         <ul class="dropdown" id="event-button-list">
            <li><a href="createEvents.php">Create Events</a></li>
            <!-- <li><a href="events.php">View Events</a></li> -->
            <li><a href="calendar.php">Calendar</a></li>
         </ul>
      </li>
      <li>
         <button class="nav-button" id="match-button" data-open="false"><img src="../public/handshake.png"
               alt="Matches page" />Matches<img class="dropdown-icon" src="../public/arrow-drop-down.svg"
               alt="Dropdown Icon" /></button>
         <ul class="dropdown" id="match-button-list">
            <li><a href="matches.php">View Matches</a></li>
            <!-- <li><a href="createGroups.php">View Group Matches</a></li> -->
            <li><a href="chatPage.php">Chat</a></li>
         </ul>
      </li>
      <li>
         <button class="nav-button" id="club-button" data-open="false"><img src="../public/group.png"
               alt="Clubs page" />Clubs<img class="dropdown-icon" src="../public/arrow-drop-down.svg"
               alt="Dropdown Icon" /></button>
         <ul class="dropdown" id="club-button-list">
            <!-- <li><a href="clubs.php">View Clubs</a></li> -->
            <li><a href="editClubList.php">View Clubs</a></li>
            <!-- <li><a href="editClubList.php">Edit Clubs</a></li> -->
         </ul>
      </li>
      <!-- <li id="calendar-list">
         <a href="calendar.php">
            <img src="../public/calendar.png" alt="Calendar Page" />Calendar</a>
      </li> -->
      <li>
         <button class="nav-button" id="account-button" data-open="false"><img src="../public/user.png"
               alt="account page" />Account<img class="dropdown-icon" src="../public/arrow-drop-down.svg"
               alt="Dropdown Icon" />
         </button>
         <ul class="dropdown" id="account-button-list">
            <li><a href="accountSettings.php">Account Settings</a></li>
            <li><a href="changeInterests.php">Edit Interests</a></li>
            <li><a href="../auth/logout.php">Log Out</a></li>
         </ul>
      </li>
   </ul>
</nav>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="scripts/nav.js"></script>
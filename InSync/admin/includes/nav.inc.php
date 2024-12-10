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
            <img src="../public/events.svg" alt="Events page " />Events <img class="dropdown-icon"
               src="../public/arrow-drop-down.svg" alt="Dropdown Icon" /></button>
         <ul class="dropdown" id="event-button-list">
            <li><a href="createEvents.php">Create Events</a></li>
         </ul>
      </li>
      <li>
         <button class="nav-button" id="account-button" data-open="false"><img src="../public/account.png"
               alt="account page" />Account<img class="dropdown-icon" src="../public/arrow-drop-down.svg"
               alt="Dropdown Icon" />
         </button>
         <ul class="dropdown" id="account-button-list">
            <li><a href="../auth/logout.php">Log Out</a></li>
         </ul>
      </li>
   </ul>
</nav>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="scripts/nav.js"></script>
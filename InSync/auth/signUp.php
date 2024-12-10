<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="resources/auth.css" />
   <title>Sign Up - InSync</title>


   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link
      href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet">


</head>

<body>
   <div class="logo-container">
      <a href="../../index.html">
         <img src="../public/logowfont.png" alt="InSync Logo" class="logo-image" />
      </a>
   </div>

   <div class="signup-container">
      <?php
      include '../includes/server.inc.php';


      // ini_set('display_errors', 1);
      // ini_set('display_startup_errors', 1);
      // error_reporting(E_ALL);

      $errors = '';
      $firstName = $lastName = $email = $password = $dob = $major = '';
      $havePost = false; // New variable to track form submission
      $emailTaken = 0;
      $rpiEmail = 1;
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
         include("../includes/gzip.inc.php");
         $havePost = true; // Form has been posted
         $firstName = htmlspecialchars(trim($_POST["first-name"]));
         $lastName = htmlspecialchars(trim($_POST["last-name"]));
         $password = htmlspecialchars(trim($_POST["password"]));
         $email = htmlspecialchars(trim($_POST["email"]));
         $dob = htmlspecialchars(trim($_POST["dob"]));
         $major = htmlspecialchars(trim($_POST["major"]));

         $focusId = '';

         if (empty($dob)) {
            $errors .= '<li>Date of birth may not be blank</li>';
            if (empty($focusId)) $focusId = '#dob';
         }

         else if($email){
            if(!str_ends_with($email, '@rpi.edu')) {
               $errors .= '<li>Please use your RPI email address (@rpi.edu)</li>';
               if (empty($focusId)) $focusId = '#email';
               $rpiEmail = 0;
           }
         }
         
         else {
            $dobDateTime = DateTime::createFromFormat('Y-m-d', $dob);
            if ($dobDateTime === false) {
               $errors .= '<li>Invalid date format. Please use YYYY-MM-DD.</li>';
               $focusId = '#dob';
            } else {
               $dob = $dobDateTime->format('Y-m-d');
            }
         }

         if (!empty($errors)) {
            // echo '<div class="messages"><h4>Please correct the following errors:</h4><ul>';
            // echo $errors;
            // echo '</ul></div>';
            // echo "<script>document.querySelector('$focusId').focus();</script>";
         } else {
            $admin = 0;
            $stmt = $conn->prepare("SELECT * FROM  admin_emails WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
               $admin = 1;
            }
            $stmt = $conn->prepare("SELECT * FROM customerdata WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
               $emailTaken = 1;
            } else {
              $encryptedPassword = sha1($password);
               $stmt = $conn->prepare("INSERT INTO customerdata (first_name, last_name, major, dob, password, email, admin) VALUES (?, ?, ?, ?, ?, ?, ?)");
               $stmt->bind_param("sssssss", $firstName, $lastName, $major, $dob, $encryptedPassword, $email, $admin);

               if ($stmt->execute()) {
                  echo "<div class='messages'><h4>Account created successfully!</h4></div>";
                  session_start();
                  $_SESSION['email'] = $email;
                  $_SESSION['password'] = $password;
                  header("Location: login.php");
               } else {
                  echo "<div class='messages'><h4>Error: " . $stmt->error . "</h4></div>";
               }

               $stmt->close();
                  $admin = 0;
                  $stmt = $conn->prepare("SELECT * FROM  admin_emails WHERE email = ?");
                  $stmt->bind_param('s', $email);
                  $stmt->execute();
               $result = $stmt->get_result();
               if ($result->num_rows > 0) {
                     $admin = 1;
                  }
                  $stmt = $conn->prepare("SELECT * FROM customerdata WHERE email = ?");
                  $stmt->bind_param('s', $email);
                  $stmt->execute();
                  $result = $stmt->get_result();
                  if ($result->num_rows > 0) {
                     $emailTaken = 1;
                  } else{
                     $encryptedPassword = sha1($password);
                     $stmt = $conn->prepare("INSERT INTO customerdata (first_name, last_name, major, dob, password, email, admin) VALUES (?, ?, ?, ?, ?, ?, ?)");
                     $stmt->bind_param("sssssss", $firstName, $lastName, $major, $dob, $encryptedPassword, $email, $admin);
                     
                     if ($stmt->execute()) {
                           echo "<div class='messages'><h4>Account created successfully!</h4></div>";
                           header("Location: login.php");
                     } else {
                           echo "<div class='messages'><h4>Error: " . $stmt->error . "</h4></div>";
                     }

                     $stmt->close();
                  }
               
            }
         }
      }
      $conn->close();
      ?>


      <form action="#" method="POST">
         <?php 
            if($emailTaken === 1){
               echo "<div class='error-wrapper'>
                        <p>Email already in use</p>
                     </div>";
            }

            if($rpiEmail === 0){
               echo "<div class='error-wrapper'>
                        <p>Email is not an RPI email</p>
                     </div>";
            }
            
            ?>
         <h1>Create a New Account</h1>
         <div class="form-main">
            <div class="names">
               <input type="text" id="first-name" name="first-name" placeholder="First Name"><input type="text"
                  id="last-name" name="last-name" placeholder="Last Name">
            </div>
            <br>

            <input type="text" id="dob" name="dob" placeholder="Date of Birth (YYYY-MM-DD)"><br><br>

            <input type="text" id="major" name="major" placeholder="Major"><br><br>

            <input type="email" id="email" name="email" placeholder="Email"><br><br>

            <input type="password" id="password" name="password" placeholder="Password">
            <br><br>
            <input type="password" id="repassword" name="repassword" placeholder="Retype Password">
            <ul id="password-checks">
               <li id="upper" class="wrong">At least one uppercase character</li>
               <li id="lower" class="wrong">At least one lowercase character</li>
               <li id="number" class="wrong">At least one number</li>
               <li id="special" class="wrong">At least one special character</li>

               <li id="match" class="wrong">Passwords must match</li>
            </ul>
            <br><br>
            <input class="submit" id="submit" type="submit" value="Sign Up" disabled>
            <p> Already have an account? <a href="login.php">Log in</a></p>
         </div>

      </form>


   </div>
   <script src="scripts/login.js"></script>
</body>

</html>
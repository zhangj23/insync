<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Account Settings - InSync</title>
   <link rel="stylesheet" href="resources/accountSettings.css" />
</head>

<body>
   <div class="sections">
      <section>
         <?php
            include("../includes/gzip.inc.php");
            include("includes/nav.inc.php");
            include("includes/redirect.php");
            include ("../includes/server.inc.php");
            $errors = "";
            if (isset($_POST['submit_email'])) {
               $old_email = $_POST['old_email'];
               $new_email = $_POST['email'];

               // Input validation
               if (empty($old_email) || empty($new_email)) {
                   $errors .= "<p style='color: red;'>Both email fields are required.</p>";
               } else {
                   // Check if old email exists in the database
                   $query = "SELECT * FROM customerdata WHERE email = ?";
                   $stmt = $conn->prepare($query);
                   $stmt->bind_param("s", $old_email);
                   $stmt->execute();
                   $result = $stmt->get_result();

                   if ($result->num_rows > 0) {
                       // User exists, proceed with email update
                       $update_query = "UPDATE customerdata SET email = ? WHERE email = ?";
                       $update_stmt = $conn->prepare($update_query);
                       $update_stmt->bind_param("ss", $new_email, $old_email);

                       if ($update_stmt->execute()) {
                           $errors .= "<p style='color: green;'>Email updated successfully!</p>";
                       } else {
                           $errors .= "<p style='color: red;'>Error updating email. Please try again.</p>";
                       }
                   } else {
                       $errors .= "<p style='color: red;'>Old email is incorrect.</p>";
                   }
               }
           }

           // Check if the password change form was submitted
           if (isset($_POST['submit_password'])) {
               $old_password = $_POST['old_password'];
               $new_password = $_POST['password'];
               // Input validation
               if (empty($old_password) || empty($new_password)) {
                   $errors .= "<p style='color: red;'>Both password fields are required.</p>";
               } else {
                  $hashed_old_password = sha1($old_password);
                   // Check if the old password matches the database
                   $query = "SELECT * FROM customerdata WHERE password = ?";
                   $stmt = $conn->prepare($query);
                   $stmt->bind_param("s", $hashed_old_password);
                   $stmt->execute();
                   $result = $stmt->get_result();

                   if ($result->num_rows > 0) {
                       // User exists, proceed with password update
                       $hashed = sha1($new_password);
                       $update_query = "UPDATE customerdata SET password = ? WHERE password = ?";
                       $update_stmt = $conn->prepare($update_query);
                       $update_stmt->bind_param("ss", $hashed, $hashed_old_password);

                       if ($update_stmt->execute()) {
                           $errors .= "<p style='color: green;'>Password updated successfully!</p>";
                       } else {
                           $errors .= "<p style='color: red;'>Error updating password. Please try again.</p>";
                       }
                   } else {
                       $errors .= "<p style='color: red;'>Old password is incorrect.</p>";
                   }
               }
            }
      ?>
      </section>

      <main>
         <header>
            <h1>Account Settings</h1>
         </header>
         <?php
         if (!empty($errors)) {
            ?>
            <div id="messages">
            <?php
            echo $errors;
            ?>
            </div>
            <?php
         }
         ?>
         <div class="formSectionWrapper">
            <form class = "formSection" action="" method="POST">
                <div class="form-item">
                    <h4>Change Email:</h4>
                    <label for="email">Old Email:</label>
                    <input type = "old_email" id ="old_email" name = "old_email"><br><br>
                    <label for="email">New Email:</label>
                    <input type="email" id="email" name="email"><br><br>
                    <br>

                    <input type = "submit" name="submit_email" value="Save Email Changes">
                </div>
            </form>
        </div>
        <div class="formSectionWrapper">
            <form class = "formSection" action="" method="POST">
                <div class="form-item">
                    <h4> Change Password:</h4><br>
                    <label for="password">Old Password:</label>
                    <input type = "password" id ="old_password" name = "old_password"><br><br>
                    <label for="password">New Password:</label>
                    <input type="password" id="password" name="password"><br><br>

                    <input type="submit" name = "submit_password"value="Save Password Changes">
                </div>
            </form>
        </div> 
         <!-- <div id="messages"> -->
      </main>
   </div>

</body>

</html>
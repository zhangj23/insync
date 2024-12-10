<?php
include("../includes/gzip.inc.php");
include '../includes/server.inc.php';

session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['admin'] == 0) {
        header("Location: ../student/home.php");
        exit();
    } else {
        header("Location: ../admin/home.php");
        exit();
    }
}

$invalid = 0;
if (isset($_SESSION['email']) && isset($_SESSION['password'])) { //! i could make this a function but im lazy
    $email = $_SESSION['email'];
    $password = $_SESSION['password'];

    unset($_SESSION['email']);
    unset($_SESSION['password']);

    $encryptedPassword = sha1($password);
    $stmt = $conn->prepare("SELECT * FROM customerdata WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $encryptedPassword);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "<div class='messages'><h4>Login successful! Redirecting...</h4></div>";

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['admin'] = $user['admin'];
        //echo "Login successful! Redirecting to home...";
        if ($_SESSION['admin'] == 1) {
            $stmt = $conn->prepare("SELECT * FROM clubs WHERE userid = ?");
            $stmt->bind_param("s", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $_SESSION['club_filled'] = 1;
                header("Location: ../admin/home.php");
                exit();
            } else {
                $_SESSION['club_filled'] = 0;
                header("Location: ../admin/clubInfo.php");
                exit();
            }
        } else {
            header("Location: ../student/editInterests.php");
            exit();
        }
    } else {
        $errors .= '<li>Invalid credentials. Please try again.</li>';
        $focusId = '#name';
    }
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$errors = $name = $password = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = htmlspecialchars(trim($_POST["email"]));
    $password = htmlspecialchars(trim($_POST["password"]));

    // Validation
    if (empty($email)) {
        $errors .= '<li>Email may not be blank</li>';
        $focusId = '#email';
    }
    if (empty($password)) {
        $errors .= '<li>Password may not be blank</li>';
        if (empty($focusId)) $focusId = '#password';
    }

    // If no errors, process login
    if (empty($errors)) {
        $encryptedPassword = sha1($password);
        $stmt = $conn->prepare("SELECT * FROM customerdata WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $encryptedPassword);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo "<div class='messages'><h4>Login successful! Redirecting...</h4></div>";

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['admin'] = $user['admin'];
            //echo "Login successful! Redirecting to home...";
            if ($_SESSION['admin'] == 1) {
                $stmt = $conn->prepare("SELECT * FROM clubs WHERE userid = ?");
                $stmt->bind_param("s", $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $club = $result->fetch_assoc();
                if ($result->num_rows > 0){
                    $_SESSION['club_filled'] = 1;
                    $_SESSION['club_id'] = $club['id'];
                    header("Location: ../admin/home.php");
                    exit();
                } else {
                    $_SESSION['club_filled'] = 0;
                    header("Location: ../admin/clubInfo.php");
                    exit();
                }
            } else {
                header("Location: ../student/home.php");
                exit();
            }
        } else {
            $invalid = 1;
            $errors .= '<li>Invalid credentials. Please try again.</li>';
            $focusId = '#name';
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>InSync - Log In</title>

   <!-- Link to an external CSS file -->
   <link rel="stylesheet" href="resources/auth.css" />
</head>

<body>
   <div class="logo-container">
      <a href="../../index.html">
         <img src="../public/logowfont.png" alt="InSync Logo" class="logo-image" />
         <!-- InSync -->
      </a>
   </div>
   <div class="login-container">

      <header>

      </header>



      <form action="#" method="POST">
         <?php 
                if($invalid === 1){
                echo "<div class='error-wrapper'>
                            <p>Invalid credentials</p>
                        </div>";
                }
            ?>
         <h1>Welcome Back</h1>

         <div class="form-main">
            <input type="email" id="email" name="email" placeholder="Email" /><br /><br />

            <input type="password" id="password" name="password" placeholder="Password" /><br /><br /><br />

            <input type="submit" value="Log In" />
            <p id="notyet"> Don't have an account? <a href="signUp.php">Sign up</a></p>
         </div>


      </form>

   </div>


   <script>
   // Automatically focus on the first field that needs correction
   <?php if (!empty($focusId)) { ?>
   document.querySelector("<?php echo $focusId; ?>").focus();
   <?php } ?>
   </script>
</body>

</html>
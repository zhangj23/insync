<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Interest Form - InSync</title>
   <link rel="stylesheet" href="resources/interest.css" />
</head>

<body>
   <?php
      include "./includes/redirect.php";
    ?>
   <header>
      <h1>Interest Form</h1>
   </header>
   <main>
      <form action="#" method="POST">
         <fieldset>
            <label for="name">Name:</label> <input id="name" type="text" />
         </fieldset>
         <fieldset>
            <label for="interests">Your Interests:</label>
            <input type="text" id="interests" name="interests" />
         </fieldset>

         <label id="personality-label" class="radio-question-label" for="personality">Select your personality
            type:</label>
         <fieldset id="radio-question">
            <div>
               <input type="radio" id="ambivert" name="personality" />
               <label for="ambivert">Ambivert</label>
            </div>
            <div>
               <input type="radio" id="introvert" name="personality" />
               <label for="introvert">Introvert</label>
            </div>
            <div>
               <input type="radio" id="extrovert" name="personality" />
               <label for="extrovert">Extrovert</label>
            </div>
         </fieldset>

         <input type="submit" value="Submit" />
      </form>
   </main>

</body>

</html>
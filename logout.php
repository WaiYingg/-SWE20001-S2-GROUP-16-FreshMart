<?php
session_start();

// Destroy the session and unset all session variables
session_unset();
session_destroy();

// Redirect the user to the login page or any other desired page
header("Location: login.php");
exit();
?>

<!DOCTYPE html>
<html>
<head>
   <title>Logout Example</title>
</head>
<body>
   <div style="max-width: 500px; margin: 0 auto; padding: 20px; text-align: center;">
      <h1>Welcome, User!</h1>
      <form action="logout.php" method="POST">
         <input type="submit" value="Logout" name="logout" style="padding: 10px 20px; background-color: #ff0000; color: #fff; border: none; cursor: pointer;">
      </form>
   </div>
</body>
</html>

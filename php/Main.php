<!DOCTYPE html>
<html>
  <!--
    CSCI 467 Group Project
    Group 9A
  -->
  <head>
    <title>Bob's Auto Parts</title>

    <?php
      // style
      include "style.html";

      // Enable error display
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);

      // Include helper functions and login info
      include "util.php";
      include "dblogin.php";
    ?>
  </head>
  
  <body>
    <h1>Welcome to Bob's Auto Parts!</h1>
    
    <form method="POST" action="Customer.php">
      <input type="submit" value="CUSTOMER" class="button button1">
    </form>

    <form method="POST" action="Packer.php">
      <input type="submit" value="PACKER" class="button button1">
    </form>

    <form method="POST" action="Reciever.php">
      <input type="submit" value="RECIEVER" class="button button1">
    </form>

    <form method="POST" action="Admin.php">
      <input type="submit" value="ADMIN" class="button button1">
    </form>

  </body>
</html>
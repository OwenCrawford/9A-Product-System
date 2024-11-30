<!DOCTYPE html>
<html>
  <!--
    CSCI 467 Group Project
    Group 9A
  -->
  <head>
    <title>Bob's Auto Parts</title>
    <style>
    /* overall layout styles */
      body {
        background-color: #f0f0f5; 
        text-align: center; 
        font-family: Arial, sans-serif;
        padding-top: 35px;
      }

    /* Button styling */
      .button {
        background-color: #04AA6D;
        border: none;
        color: white;
        padding: 16px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        transition-duration: 0.4s;
        cursor: pointer;
      }

      .button1 {
        background-color: white;
        color: black;
        border: 2px solid #9132A8;
        border-radius: 12px;
      }

      .button1:hover {
        background-color: #9132A8;
        color: white;
      }

      .button2 {
        background-color: white;
        color: black;
        border: 2px solid #376DA6;
        border-radius: 12px;
      }

      .button2:hover {
        background-color: #376DA6;
        color: white;
      }
    </style>

    <?php
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
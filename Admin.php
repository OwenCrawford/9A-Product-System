<!DOCTYPE html>
<html>
    <head>
        <title>Admin View</title>
        <?php 
            //style
            include "style.html";

            //enable error display
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            include "util.php";
        ?>
    </head>
    
    <body>
        <h1>Welcome, Administrator!</h1>

        <form method="POST" action="UpdateShipping.php">
        <input type="submit" value="Shipping Charges" class="button button1">
        </form>

        <form method="POST" action="OrderList.php">
        <input type="submit" value="View Orders" class="button button1">
        </form>
    </body>
</html>
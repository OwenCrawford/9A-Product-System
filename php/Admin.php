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
            include "dblogin.php"; 
            include "util.php";
            include "queries.php";
        ?>
    </head>
    
    <body>
        <h1>Welcome, Administrator!</h1>

        <?php
            //initialize database connection
            try {
                $pdo = new PDO($invdbDsn, $invdbUser, $invdbPass);
                echo "Connected to database!<br>";
            }
            catch(PDOexception $e) {
                echo "Could not connect to database: " . $e->getMessage() . "<br>"; 
            }
        ?>

        <p>
        <h3>Order List:</h3>
        <?php
            $sortdir = "DESC";
            $sortcol = "timePlaced";
            GetSortParams("orders", $sortcol, $sortdir);
            
            $result = $pdo->query(OrderListQuery($sortcol,$sortdir));
            PrintTable($result, array("Order Number","Time Placed","Order Status","Price"),
                true, "Admin.php", "orders", 
                $sortcol, $sortdir, array("Order Number","Time Placed","Order Status","Price"), 
                "View Details", "orderNum", "" );
        ?>
        </p>
    </body>
</html>
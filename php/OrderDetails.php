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

            //Include helper functions and login info
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
                $invpdo = new PDO($invdbDsn, $invdbUser, $invdbPass);
                $partpdo = new PDO($legacydbDsn, $legacydbUser, $legacydbPass);
                //echo "Connected to databases!<br>";
            }
            catch(PDOexception $e) {
                echo "Could not connect to database: " . $e->getMessage() . "<br>"; 
            }
        ?>

        <form method="POST" action="OrderList.php">
            <input type="submit" value="Back" class="button button1">
        </form>

        <p>
        <h3>Order Details:</h3>
        <?php
            
            if(key_exists("orders_choice",$_GET)) {
                $orderNum = $_GET["orders_choice"];
            } else {
                $orderNum = -1;
            }

            //fetch list of parts in order
            $invresult = $invpdo->query(OrderPartsListQuery($orderNum));
            $partnums = $invresult->fetchAll(PDO::FETCH_COLUMN, 0);
            if(count($partnums) > 0) {
                //fetch corresponding info from legacy DB
                $legresult = $partpdo->query(PartDetailQuery($partnums));
                $partInfo = $legresult->fetchAll();

                //merge the two into one array
                $invresult = $invpdo->query(OrderDetailQuery($orderNum));
                $orderdetail = MergePartDetails($invresult,$partInfo);

                //display the resulting table
                echo BuildTableFromArray($orderdetail, 
                    ["Part Number", "Quantity", "Description", "Price", "Weight", "Image"]);
            } else {
                echo "<h3>Warning: no parts found in order.</h3>";
            }
        ?>
        </p>
    </body>
</html>

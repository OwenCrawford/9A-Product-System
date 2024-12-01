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
                $invpdo = new PDO($invdbDsn, $invdbUser, $invdbPass);
                $partpdo = new PDO($legacydbDsn, $legacydbUser, $legacydbPass);
                //echo "Connected to databases!<br>";
            }
            catch(PDOexception $e) {
                echo "Could not connect to database: " . $e->getMessage() . "<br>"; 
            }
        ?>

        <a href="OrderList.php">Back</a>

        <p>
        <h3>Order Details:</h3>
        <?php
            
            if(key_exists("orders_choice",$_GET)) {
                $orderNum = $_GET["orders_choice"];
            } else {
                $orderNum = -1;
            }
            
            $invresult = $invpdo->query(OrderPartsListQuery($orderNum));
            $partnums = $invresult->fetchAll(PDO::FETCH_COLUMN, 0);
            $legresult = $partdbo->query(PartDetailQuery($partnums));
            $partInfo = $legresult->fetchAll();
            $invresult = $invpdo->query(OrderDetailQuery($orderNum));

            $orderdetail = MergePartDetails($invresult,$partinfo);
            echo BuildTableFromString($orderdetail, 
                ["Part Number", "Quantity", "Description", "Price", "Weight", "Image"]);


        ?>
        </p>
    </body>
</html>
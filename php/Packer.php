<!DOCTYPE html>
<html>
    <head>
        <title>Packer View</title>
        <?php 
            //style
            include "style.html";

            //enable error display
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            //include common files
            include "dblogin.php"; 
            include "util.php";
            include "queries.php";
        ?>
    </head>
    
    <body>
        <h1>Welcome, Employee!</h1>
        <form method="POST" action="Main.php">
            <input type="submit" value="Back" class="button button1">
        </form>

        <?php
            //initialize database connection
            try {
                $invpdo = new PDO($invdbDsn, $invdbUser, $invdbPass);
                //echo "Connected to database!<br>";
            }
            catch(PDOexception $e) {
                echo "Could not connect to database: " . $e->getMessage() . "<br>"; 
            }

            //update a packed order's status
            if ($_SERVER["REQUEST_METHOD"] == "POST" && key_exists("selection",$_POST) && $_POST["selection"] == "Fill Order") {
                $result = $invpdo->query(UpdateOrderStatusQuery($_POST["orderNum"], "shipped"));
                $result->closeCursor();
                $result = $invpdo->query(OrderDetailQuery($_POST["orderNum"]));
                $partlist = $result->fetchAll(PDO::FETCH_NUM);
                foreach($partlist as $part) {
                    $result = $invpdo->query(RemovePartQuery($part[0], $part[1]));
                    $result->closeCursor();
                }
                echo "<p style=\"background-color:green;\">Order #" . $_POST["orderNum"] . " status updated.</p>";
            }
        ?>

        <p>
        <h3>Order List:</h3>
        <?php
            $sortdir = "ASC";
            $sortcol = "timePlaced";
            GetSortParams("orders", $sortcol, $sortdir);
            
            //display sortable list of orders
            $result = $invpdo->query(OrderPackingListQuery($sortcol,$sortdir));
            PrintTable($result, array("Order Number","Time Placed","Order Status","Price"),
                true, "Packer.php", "orders", 
                $sortcol, $sortdir, array("Order Number","Time Placed","Order Status","Price"), 
                "View Details", "orderNum", "OrderPacking.php" );
        ?>
        </p>
    </body>
</html>
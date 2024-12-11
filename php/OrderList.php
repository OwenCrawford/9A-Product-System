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

        <form method="POST" action="Admin.php">
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
        ?>

        <p>
        <h3>Order List:</h3>
        
        <!-- search fields -->
        <form method="POST" action="OrderList.php">
            <p>
            <label for="datesearch1">Search by date range:</label>
            <input type="date" id="datesearch1" name="datesearch1" 
            <?php if(key_exists("datesearch1", $_POST))
                    echo "value=\"" . $_POST["datesearch1"] . "\""; ?> >
            <input type="date" id="datesearch2" name="datesearch2"
            <?php if(key_exists("datesearch2", $_POST))
                    echo "value=\"" . $_POST["datesearch2"] . "\""; ?> >
            </p>

            <p>
            <label for="pricesearch1">Search by price range:</label>
            <input type="number" id="pricesearch1" name="pricesearch1" step=.01
            <?php if(key_exists("pricesearch1", $_POST))
                    echo "value=\"" . $_POST["pricesearch1"] . "\""; ?> >
            <input type="number" id="pricesearch2" name="pricesearch2" step=.01
            <?php if(key_exists("pricesearch2", $_POST))
                    echo "value=\"" . $_POST["pricesearch2"] . "\""; ?> >
            </p>
            
            <p>
            <label for="statussearch">Search by status:</label>
            <?php MakeSelect("statussearch", ["", "authorized", "shipped"], ["Select an option", "authorized", "shipped"], true, false); ?>
            </p>
            <input type="submit" value="Search">
        </form><br>

        <?php
            //fetch sort & search params, if they exist
            $sortdir = "DESC";
            $sortcol = "timePlaced";
            GetSortParams("orders", $sortcol, $sortdir);
            $q = OrderListQuery($sortcol,$sortdir);
            if (exists("datesearch1") && exists("datesearch2")) {
                $q = OrderListDateSearchQuery($sortcol, $sortdir, $_POST["datesearch1"] . " 00:00:00", $_POST["datesearch2"] . " 00:00:00");
            } else if (exists("pricesearch1") && exists("pricesearch2")) {
                $q = OrderListPriceSearchQuery($sortcol, $sortdir, $_POST["pricesearch1"], $_POST["pricesearch2"]);
            } else if (exists("statussearch")) {
                $q = OrderListStatusSearchQuery($sortcol, $sortdir, $_POST["statussearch"]);
            }

            //display found parts
            $result = $invpdo->query($q);
            PrintTable($result, array("Order Number","Time Placed","Order Status","Price"),
                true, "Admin.php", "orders", 
                $sortcol, $sortdir, array("Order Number","Time Placed","Order Status","Price"), 
                "View Details", "orderNum", "OrderDetails.php" );

            function exists($key) {
                return key_exists($key, $_POST) && $_POST[$key] != "";
            }
        ?>

        </p>
    </body>
</html>

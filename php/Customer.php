<!DOCTYPE html>
<html>
    
    <head>
        <title>Customer View</title>
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
        <?php
            //initialize database connection
            try {
                $legpdo = new PDO($legacydbDsn, $legacydbUser, $legacydbPass);
                $invpdo = new PDO($invdbDsn, $invdbUser, $invdbPass);
                //echo "Connected to database!<br>";
            }
            catch(PDOexception $e) {
                echo "Could not connect to database: " . $e->getMessage() . "<br>"; 
            }
        ?>

    </head>
    
    <body>
        <h1>Welcome to Bob's Auto Parts</h1>
        <form method="POST" action="Main.php">
            <input type="submit" value="Back" class="button button1">
        </form>

        <?php
            //get search & sort params, if they exist
            $sortdir = "ASC";
            $sortcol = "number";
            $searchstr = "";
            if(key_exists("searchstr", $_POST))
                $searchstr = $_POST["searchstr"]; 

            //fetch list of parts
            $result = $legpdo->query(PartListSearchQuery($sortcol,$sortdir,$searchstr));
            $tablestr = BuildTable($result, array("Part Number","Description","Price", "Weight", "Image"),
                false, "", "parts", "", "", [], "", "", "",
                true, "number", "Enter Quantity:" );
            
            //replace image URLs with an <img> element
            $tablestr = preg_replace( "~(http://blitz.cs.niu.edu/pics/)(\S+?.jpg)~", 
                "<img src=\"$1$2\" alt=\"\\2\" >",
                $tablestr);
        ?>

        <!-- display the table with a submit button -->
        <form method="post" action="Cart.php">
            <input type="submit" name="Cart" value="Add to cart" />
            <p><?php echo $tablestr; ?></p>
        </form>
    </body>
</html>

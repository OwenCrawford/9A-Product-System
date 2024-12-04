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
        <h1>Bob's Auto Parts</h1>
        <?php
            $sortdir = "ASC";
            $sortcol = "number";
            $searchstr = "";
            if(key_exists("searchstr", $_POST))
                $searchstr = $_POST["searchstr"];
            //GetSortParams("parts", $sortcol, $sortdir);
            //var_dump($_POST);     
            $result = $legpdo->query(PartListSearchQuery($sortcol,$sortdir,$searchstr));
            $tablestr = BuildTable($result, array("Part Number","Description","Price", "Weight", "URL"),
                false, "", "parts", "", "", [], "", "", "",
                true, "number", "Enter Quantity:" );
            
            $tablestr = preg_replace( "~(http://blitz.cs.niu.edu/pics/)(\S*.jpg)~", 
                "<img src=\"$1$2\" alt=\"\\2\" >",
                $tablestr);
        ?>

        <?php
            if(key_exists("add", $_POST)) {
                $keys = array_keys($_POST);
                $invlist = $invpdo->query(InventoryListQuery());
                $invlist = $invlist->fetchAll(PDO::FETCH_NUM);
                $invlist = FlattenArray($invlist);

                foreach($keys as $k) {
                    if(preg_match("~number_\d+~", $k) && $_POST[$k] != 0) {
                        $num = preg_replace("~number_(\d+)~", "\\1", $k);
                        $add = 0 + $_POST[$k];
                        $qty = $add;
                        $new = true;
                        if(key_exists($num, $invlist)) {
                            $qty += $invlist[$num];
                            $new = false;
                        }
                        $result = $invpdo->query(UpdatePartQuery($num, $qty, $new));
                        if($add > 0)
                            echo "<p style=\"background-color:green;\">Added " 
                                . $qty . " of part #" . $num . ".</p>";
                        else
                            echo "<p style=\"background-color:green;\">Removed " 
                                . $qty . " of part #" . $num . ".</p>";
                    }
                }
            }
        ?>

        <form method="post" action="Cart.php">
            <input type="submit" name="Cart" value="Add to cart" />
            <p><?php echo $tablestr; ?></p>
        </form>
    </body>
</html>

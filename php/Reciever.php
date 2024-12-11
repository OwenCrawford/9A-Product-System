<!DOCTYPE html>
<html>
    <head>
        <title>Reciever View</title>
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
                $legpdo = new PDO($legacydbDsn, $legacydbUser, $legacydbPass);
                $invpdo = new PDO($invdbDsn, $invdbUser, $invdbPass);
                //echo "Connected to database!<br>";
            }
            catch(PDOexception $e) {
                echo "Could not connect to database: " . $e->getMessage() . "<br>"; 
            }
        ?>

        <p>
        <h3>Part List:</h3>
        
        <!-- search box -->
        <form method="POST" action="Reciever.php">
            <label for="searchstr">Search part number or description:</label>
            <input type="text" id="searchstr" name="searchstr"
            <?php if(key_exists("searchstr", $_POST))
                    echo "value=\"" . $_POST["searchstr"] . "\""; ?> >
            <input type="submit" value="Search">
        </form><br>

        <!-- table of parts -->
        <?php
            $sortdir = "ASC";
            $sortcol = "number";
            $searchstr = "";
            if(key_exists("searchstr", $_POST))
                $searchstr = $_POST["searchstr"];
  
            $result = $legpdo->query(PartListSearchQuery($sortcol,$sortdir,$searchstr));

            $tablestr = BuildTable($result, array("Part Number","Description","Price", "Weight", "Image"),
                false, "", "parts", "", "", [], "", "", "",
                true, "number", "Enter Quantity:" );
            
            //replace image URLs with <img> element
            $tablestr = preg_replace( "~(http://blitz.cs.niu.edu/pics/)(\S+?.jpg)~", 
                "<img src=\"\\1\\2\" alt=\"\\2\">",
                $tablestr);
        ?>

        <?php
            if(key_exists("add", $_POST)) {
                //add selected parts to inventory
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

                                . $add . " of part #" . $num . ".</p>";
                        else
                            echo "<p style=\"background-color:green;\">Removed " 
                                . $add . " of part #" . $num . ".</p>";
                    }
                }
            }
        ?>

        <!-- submit button -->
        <form method="POST" action="Reciever.php">
            <input type="submit" name="add" value="Add to Inventory">
            <p><?php echo $tablestr; ?></p>
        </form>
        </p>
    </body>
</html>

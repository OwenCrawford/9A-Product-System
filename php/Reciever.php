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
            include "dblogin.php"; 
            include "util.php";
            include "queries.php";
        ?>
    </head>
    
    <body>
        <h1>Welcome, Employee!</h1>

        <?php
            //initialize database connection
            try {
                $pdo = new PDO($legacydbDsn, $legacydbUser, $legacydbPass);
                echo "Connected to database!<br>";
            }
            catch(PDOexception $e) {
                echo "Could not connect to database: " . $e->getMessage() . "<br>"; 
            }
        ?>

        <p>
        <h3>Part List:</h3>
        <?php
            $sortdir = "ASC";
            $sortcol = "number";
            //GetSortParams("parts", $sortcol, $sortdir);
            //var_dump($_POST);     
            $result = $pdo->query(PartListQuery($sortcol,$sortdir));
            $tablestr = BuildTable($result, array("Part Number","Description","Price", "Weight", "URL"),
                false, "", "parts", 
                "", "", [], 
                "", "", "",
                true, "number", "Enter Quantity:" );
            
            $tablestr = preg_replace( "~(http://blitz.cs.niu.edu/pics/)(\S*.jpg)~", 
                "<img src=\"$1$2\" alt=\"\\2\" >",
                $tablestr);
        ?>

        <form method="POST" action="Reciever.php">
            <input type="submit" value="Add to Inventory">
            <p><?php echo $tablestr; ?></p>
        </form>
        </p>
    </body>
</html>
<!DOCTYPE html>
<html>
    <head>
        <title>Cart</title>
    </head>
    
    <body>
        <form method='POST' action='Customer.php'>
            <input type="submit" name="Back" value="Back To Customer Page" />
        </form>
        <h1>Cart</h1>
        <?php 
            $numlist = []; 
            $qtyList = [];
            $n = 0; 
            $price = 0;
            $weight = 0;
            $fee = 0;
            $total = 0;
            calculatePartInfo($numlist, $qtylist, $n); ?>
        <h2>You have <?php echo $n?> item<?php if ($n!=1) {echo "s";}?> in your cart</h2>
        <?php displayParts($numlist, $qtylist, $price, $weight, $fee, $total); ?>
        <h2>Billing Information:</h2>
        <h3>Amount: $<?php echo $price?></h3>
        <h3>Weight: <?php echo $weight?> lbs</h3>
        <h3>Shipping and Handling: $<?php echo $fee?></h3>
        <h3>Total: $<?php echo $total?></h3>
        <form method="post" action="ConfirmOrder.php">
            <label for="Name">Name:</label>
            <input type="text" name="Name" required /></p>
            <label for="Email">Email:</label>
            <input type="text" name="Email" required /></p>
            <label for="Address">Address:</label>
            <input type="text" name="Address" required /></p>
            <label for="CC">Credit Card Number:</label>
            <input type="text" name="CC" required /></p>
            <label for="ExpDate">Exp. Date:</label>
            <input type="month" name="ExpDate" required /></p>
            <input type="submit" name="Purchase" value="Purchase" />
            <input type="hidden" name="Amount" value=<?php echo $total ?> />
        </form>
<?php 
    function calculatePartInfo(&$numlist, &$qtylist, &$n) {
        $keys = array_keys($_POST);
        foreach($keys as $k) {
            if(preg_match("~number_\d+~", $k) && $_POST[$k] != 0) {
                $num = preg_replace("~number_(\d+)~", "\\1", $k);
                array_push($numlist, $num);
                $qtylist[$n+1] = $_POST[$k];
                $n++;
            }
        }
        if ($n == 0) {
            //redirect
            header("Location: Customer.php");
        }
    }

    function displayParts($numlist, &$qtylist, &$price, &$weight, &$fee, &$total) {
        //style
        include "style.html";

        //enable error display
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        include "dblogin.php"; 
        include "util.php";
        include "queries.php";
        //initialize database connection
        try {
            $legpdo = new PDO($legacydbDsn, $legacydbUser, $legacydbPass);
            $invpdo = new PDO($invdbDsn, $invdbUser, $invdbPass);
            //echo "Connected to database!<br>";
        }
        catch(PDOexception $e) {
            echo "Could not connect to database: " . $e->getMessage() . "<br>"; 
        }
        $result = $legpdo->query(PartListNumericalSearchQuery("number","ASC",$numlist));
        $tablestr = BuildTable($result, array("Part Number","Description","Price", "Weight", "URL"),
            false, "", "parts", "", "", [], "", "", "",
            false, "", "", [], $price, $weight, $qtylist, true);
        
        $tablestr = preg_replace( "~(http://blitz.cs.niu.edu/pics/)(\S+?.jpg)~", 
            "<img src=\"\\1\\2\" alt=\"\\2\" >",
            $tablestr);
        echo $tablestr;

        //get fee
        $invresult = $invpdo->query(ShippingChargeQuery());
        $partnums = $invresult->fetchAll(PDO::FETCH_NUM);
        foreach($partnums as $row) {
            $fee = $row[2];
            if ($weight <= $row[1]) {
                break;
            }        
        }
        $total = $fee + $price;

        
    }
?>
    </body>
</html>


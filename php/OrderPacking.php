<!DOCTYPE html>
<html>
    <head>
        <title>Bob's Warehouse</title>
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
                $invpdo = new PDO($invdbDsn, $invdbUser, $invdbPass);
                $partpdo = new PDO($legacydbDsn, $legacydbUser, $legacydbPass);
                //echo "Connected to databases!<br>";
            }
            catch(PDOexception $e) {
                echo "Could not connect to database: " . $e->getMessage() . "<br>"; 
            }
        ?>

        <form method="POST" action="Packer.php">
            <input type="submit" value="Back" class="button button1">
        </form>

        <p>
        <h3>Packing List:</h3>
        <?php
            
            if(key_exists("orders_choice",$_GET)) {
                $orderNum = $_GET["orders_choice"];
            } else {
                $orderNum = -1;
            }
            
            $invresult = $invpdo->query(OrderPartsListQuery($orderNum));
            $partnums = $invresult->fetchAll(PDO::FETCH_COLUMN, 0);
            $legresult = $partpdo->query(PartDetailQuery($partnums));
            $partInfo = $legresult->fetchAll();
            $invresult = $invpdo->query(OrderDetailQuery($orderNum));

            $orderdetail = MergePartDetails($invresult,$partInfo);
            //var_dump( $orderdetail);
            echo BuildTableFromArray($orderdetail, 
                ["Part Number", "Quantity", "Description", "Price", "Weight", "Image"]);
     
        ?>
        </p>
        <p style="text-align:left">
            <h3>Invoice:</h3>
        <?php
            $subtotal = 0;
            $weight = 0;
            foreach($orderdetail as $part) {
                $subtotal += ($part[1] * $part[3]);
                $weight += ($part[1] * $part[4]);
                echo $part[2] . "(x" . $part[1] . "): " . ($part[1] * $part[3]) . "<sp>";
            }
            echo "Subtotal: " . $subtotal . "<sp>";
            $invresult = $invpdo->query(ShippingChargeQuery());
            $shipping = 0.00;
            for($r = 0; $r < $result->rowCount(); $r++) {
                $row = $result->fetch(PDO::FETCH_NUM);
                if($shipping == 0 && $weight < $row[1]) {
                    $shipping = $row[2];
                }
            }
            echo "Shipping: " . $shipping . "<sp>";
            $total = $subtotal + $shipping;
            echo "Total: " . $total . "<sp>";
        ?>
        </p>
        <p style="text-align:left">
            <h3>Shipping Label:</h3>
        <?php
            $invresult = $invpdo->query(CustomerInfoQuery($orderNum));
            $custinfo = $invresult->fetch(PDO::FETCH_NUM);
            echo $custinfo[1] . "<sp>";
            echo $custinfo[3] . "<sp>";
            echo "Confirmation will be sent to: " . $custinfo[2] . "<sp>";
        ?>
        </p>
        <p>
            <form method="POST" action="Packer.php">
                <input type="hidden" id="ordreNum" name="orderNum" value=<?php echo $orderNum; ?>>
                <input type="submit" name="selection" value="Fill Order">
                <input type="submit" name="selection" value="Cancel">
            </form>
        </p>

    </body>
</html>
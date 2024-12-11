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
            
            //fetch order to display
            if(key_exists("orders_choice",$_GET)) {
                $orderNum = $_GET["orders_choice"];
            } else {
                $orderNum = -1;
            }
            
            //display order info
            $invresult = $invpdo->query(OrderPartsListQuery($orderNum));
            $partnums = $invresult->fetchAll(PDO::FETCH_COLUMN, 0);
            if(count($partnums) > 0) {
                $legresult = $partpdo->query(PartDetailQuery($partnums));
                $partInfo = $legresult->fetchAll();
                $invresult = $invpdo->query(OrderDetailQuery($orderNum));

                $orderdetail = MergePartDetails($invresult,$partInfo);
                echo BuildTableFromArray($orderdetail, 
                    ["Part Number", "Quantity", "Description", "Price", "Weight", "Image"]);
            } else {
                echo "<h3>Warning: no parts found in order.</h3>";
            }
        ?>
        </p>
        <p>
            <h3>Invoice:</h3>
        <?php
        //calculate shipping and total charge
        //check if sufficient parts are available to pack
        if(count($partnums) > 0) {
            $subtotal = 0;
            $weight = 0;
            $invresult = $invpdo->query(InventoryListQuery($partnums));
            $invlist = $invresult->fetchAll(PDO::FETCH_NUM);
            $canFill = true;
            foreach($orderdetail as $part) {
                $onHand = MatchFirstElement($invlist, $part[0]);
                if(!$onHand || $onHand[1] < $part[1])
                    $canFill = false;
                $subtotal += ($part[1] * $part[3]);
                $weight += ($part[1] * $part[4]);
                echo $part[2] . "(x" . $part[1] . "): " . ($part[1] * $part[3]) . "<br>";
            }
            echo "Subtotal: " . $subtotal . "<br>";
            $invresult = $invpdo->query(ShippingChargeQuery());
            $shipping = 0.00;
            for($r = 0; $r < $invresult->rowCount(); $r++) {
                $row = $invresult->fetch(PDO::FETCH_NUM);
                if($shipping == 0 && $weight < $row[1]) {
                    $shipping = $row[2];
                }
            }
            echo "Shipping: " . $shipping . "<br>";
            $total = $subtotal + $shipping;
            echo "Total: " . $total . "<br>";
        }
        ?>
        </p>
        <p>
            <h3>Shipping Label:</h3>
        <?php
        //display customer information
        if(count($partnums) > 0) {
            $invresult = $invpdo->query(CustomerInfoQuery($orderNum));
            $custinfo = $invresult->fetch(PDO::FETCH_NUM);
            echo $custinfo[1] . "<br>";
            echo $custinfo[3] . "<br>";
            echo "Confirmation will be sent to: " . $custinfo[2] . "<br>";
        ?>
        </p>

        <!-- confirmation buttons -->
        <p>
            <form method="POST" action="Packer.php">
                <input type="hidden" id="orderNum" name="orderNum" value=<?php echo $orderNum; ?>>
                <input type="submit" name="selection" value="Fill Order" 
                    <?php if(!$canFill) echo " disabled='true'"; ?> >
                <input type="submit" name="selection" value="Cancel">
            </form>
        </p>
        <?php
            if(!$canFill) {
                echo "<h3>Insufficient inventory to pack order. Please contact recieving desk.</h3>";
            }
        }
        ?>

    </body>
</html>
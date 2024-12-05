<html>
    <head>
        <title>Bob's Auto Parts</title>
        <?php
            // style
            include "style.html";

            // Enable error display
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            // Include helper functions and login info
            include "util.php";
            include "dblogin.php";
            include "queries.php";
        ?>
    </head>

    <body>
        <?php
        $url = 'http://blitz.cs.niu.edu/CreditCard/';
        $expdate = $_POST["ExpDate"];
        //$expdate = DateTime::createFromFormat("Y-m", $expdate)->format("m/Y");
        $data = array(
            'vendor' => 'VE001-03',
            'trans' => generateRandomString(),
            'cc' => $_POST["CC"],
            'name' => $_POST["Name"], 
            'exp' => $expdate, 
            'amount' => $_POST["Amount"]);

        $options = array(
            'http' => array(
                'header' => array('Content-type: application/json', 'Accept: application/json'),
                'method' => 'POST',
                'content'=> json_encode($data)
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $result = json_decode($result, true);


        echo "<h1>Order Confirmation</h1>";
        if (array_key_exists('errors', $result)) {
            echo "<h2>Error confirming your order: ".$result["errors"][0]."</h1>";
        } else {
            try {
                $invpdo = new PDO($invdbDsn, $invdbUser, $invdbPass);
            } catch(PDOexception $e) {
                echo "Could not connect to database: " . $e->getMessage() . "<br>"; 
            }
            $result = $invpdo->query(CustomerSearchQuery($_POST["Name"], $_POST["Email"], $_POST["Address"]));
            if($result->rowCount() > 0) {
                //existing customer
                $custID = ($result->fetch(PDO::FETCH_NUM))[0];
                $result->closeCursor();
            } else {
                //new customer
                $result = $invpdo->query(AddCustomerQuery($_POST["Name"], $_POST["Email"], $_POST["Address"]));
                $custID = $invpdo->lastInsertID();
                $result->closeCursor();
            }
            $result = $invpdo->query(AddOrderQuery('authorized', $_POST["Amount"], $custID));
            $orderID = $invpdo->lastInsertID();
            $result->closeCursor();

            echo "<h2>Order successfully completed. Thank you for shopping at Bob's Auto Parts!</h2>";
            echo "<h3>Your order is #$orderID</h3>";
        }
        

        function generateRandomString($length = 20) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-';
            $charactersLength = strlen($characters);
            $randomString = '';

            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[random_int(0, $charactersLength - 1)];
            }

            return $randomString;
        }
        ?>
    <?php 
    /*
        if (!array_key_exists('errors', $result)) {
          echo "<h3>Total Price: $" . $_POST["Amount"]."</h3>";
          echo "<h3>Name: ". $_POST["Name"] ."</h3>";
          echo "<h3>Email: ". $_POST["Email"]."</h3>";
          echo "<h3>Auth: ". $result["authorization"]."</h3>";
        }
          */
    ?>
    <form method='POST' action='Customer.php'>
        <input type="submit" name="Back" value="Back To Customer Page" />
    </form>
    </body>
</html>

<!DOCTYPE html>
<html>
  <!--
    CSCI 467 Group Project
    Group 9A
  -->
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
      include "queries.php";
      include "dblogin.php";
    ?>
  </head>
  
  <body>
    <h1>Welcome to Bob's Auto Parts!</h1>

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
    
    <form method="POST" action="Admin.php">
      <input type="submit" value="BACK" class="button button1">
    </form>

    <?php
         if ($_SERVER["REQUEST_METHOD"] == "POST" && key_exists("updated",$_POST)) {
            $brackets = array_keys($_POST);
            $brackets = array_values(array_filter($brackets, function(string $e) {return preg_match("~(\S+)_cut~", $e);}));
            for($b = 0; $b < count($brackets); $b++) {
                $brackets[$b] = preg_replace("~(\S+)_cut~", "\\1", $brackets[$b]);
            }
            $prevcut = -1;
            $valid = true;
            $updates = [];
            foreach($brackets as $b) {
                if($valid && $_POST[$b . "_cut"] <= $prevcut) {
                    echo "<p style=\"background-color:red;\">Each bracket cutoff should be greater than the one before it!</p>";
                    $valid = false;
                } else if($valid && key_exists($b . "_chg", $_POST)) {
                    //number_format fixes bug where 5 is not accepted by sql but 5.00 is
                    $updates[] = [$b, $_POST[$b . "_cut"], number_format($_POST[$b . "_chg"],2)];
                    $prevcut = $_POST[$b . "_cut"];
                }
            }
            if($valid) {
                foreach($updates as $u)
                    $invpdo->query(UpdateShippingQuery($u));
                echo "<p style=\"background-color:green;\">Weight brackets updated!</p>";
            }
         }
    ?>
    
    <form method="POST" action="UpdateShipping.php">
        <input type="hidden" name="updated" value=true>
        <table border=1> <tr>
            <th>Weight Bracket</th>
            <th>Maximum Weight</th>
            <th>Shipping Charge</th>
        </tr>
        <?php
            $chargesResult = $invpdo->query(ShippingChargeQuery());
            for($r = 0; $r < $chargesResult->rowCount(); $r++) {
                $row = $chargesResult->fetch(PDO::FETCH_NUM);
                echo "<tr><td>" . $row[0] . "</td>";
                echo "<td><input type=\"number\" id=\"" . $row[0] . "_cut" 
                    . "\" name=\"" . $row[0] . "_cut\" value=\"" 
                    . $row[1] . "\" step=\"0.01\" required></td>";
                echo "<td><input type=\"number\" id=\"" . $row[0] . "_chg" 
                    . "\" name=\"" . $row[0] . "_chg\" value=\""
                    . $row[2] . "\" step=\"0.01\" required></td><tr>";
            }
        ?>
        </table>
        <input type="submit" value="Submit Changes" class="button button1">
  </body>
</html>

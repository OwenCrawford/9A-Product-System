<?php
   
  //print a nice table from a query result
  function PrintTable(PDOStatement $result, array $colnames = [], 
      Bool $sortable = false, String $pageadr = "", String $tablename = "t", 
      String $sortcol = "", String $sortdir = "ASC", array $sortablecols = [],
      String $selectlbl = "", String $selectvar = "", String $selectpage = "") {
    
    //column headers
    echo "<table border=1> <tr>";
    $selectcol = -1;
    for($c = 0; $c < $result->columnCount(); $c++) {
      $colname = $result->getColumnMeta($c)["name"];
      if($colname != $selectvar || $selectcol != -1) {
        if(count($colnames) > $c) {
          $coldisp = $colnames[$c];
        } else {
          $coldisp = $colname;
        }
        if($sortable && (count($sortablecols) == 0 || in_array($coldisp,$sortablecols)) ) {
          $sortico = "";
          $newdir = "ASC";
          if($sortcol == $colname){
            $sortico = " &darr;";
            if($sortdir == "ASC") {
              $sortico = " &uarr;";
              $newdir = "DESC";
            }
          }
          $colstr = "<a href=\"?" 
              . GetToString([$tablename . "_sortcol", $tablename . "_sortdir"]) 
              . $tablename . "_sortcol=" . $colname . "&"
              . $tablename . "_sortdir=" . $newdir . "\">"
              . $coldisp . $sortico . "</a>";
        } else {
          $colstr = $coldisp;
        }
        echo "<th>$colstr</th>";
      } else {
        $selectcol = $c;
      }
    }
    if($selectlbl != "") {
      echo "<th></th>";
    }
    echo "</tr>";
    
    
    //read each row
    for($r = 0; $r < $result->rowCount(); $r++) {
      $row = $result->fetch();
      echo "<tr>";
      for($c = 0; $c < $result->columnCount(); $c++) {
        if($c != $selectcol)
          echo "<td>$row[$c]</td>";
      }
      if($selectlbl != "" && $selectcol >= 0) {
        echo "<td><a href=\"" . $selectpage . "?" . GetToString([$tablename . "_choice"]) 
            . $tablename . "_choice=" . $row[$selectvar]
            . "\">" . $selectlbl . "</a></td>";
      }
      echo "</tr>";
    } 
    echo "</table>";
  }
  
  
  //create a select form element with 
  //an option for each value in an array
  function MakeSelect(string $name, array $values, 
        array $labels, bool $keepContents = false, bool $useget = false) {
    
    $source =& $_POST;
    if($useget)
      $source =& $_GET;
      
    //escape special characters
    $name = htmlspecialchars($name);
    
    echo "<select id=\"$name\" name=\"$name\">";
    for($i = 0; $i < count($values) && $i < count($labels); $i++) {
    
      //escape special characters
      $values[$i] = htmlspecialchars($values[$i]);
      $labels[$i] = htmlspecialchars($labels[$i]);
      
      echo "<option ";
      //carry forward form data if applicable
      if($keepContents && key_exists($name,$source) && $source[$name] == $values[$i])
        echo "selected ";
      echo "value=\"$values[$i]\">$labels[$i]</option>";
    }
    echo "</select>";
    
  }
  
  //get current sort parameters, if they exist
  function GetSortParams(string $tablename, &$sortcol, &$sortdir) {
    if(key_exists($tablename . "_sortcol",$_GET)) {
      $sortcol = $_GET[$tablename . "_sortcol"];
      if(key_exists($tablename . "_sortdir",$_GET)) {
        $sortdir = $_GET[$tablename . "_sortdir"];
      }
    }
  }
  
  function GetToString(array $ignore = []) {
    $getstr = "";
    foreach($_GET as $key => $val) {
      if(!in_array($key, $ignore))
        $getstr = $getstr . $key . "=" . $val . "&";
    }
    
    //if(strlen($getstr) > 0)
      //$getstr = rtrim($getstr, "&");
    
    return $getstr;
  }
  
?>

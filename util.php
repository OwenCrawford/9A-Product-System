<?php
   
  function PrintTable(PDOStatement $result, array $colnames = [], 
    Bool $sortable = false, String $pageadr = "", String $tablename = "t", 
    String $sortcol = "", String $sortdir = "ASC", array $sortablecols = [],
    String $selectlbl = "", String $selectvar = "", String $selectpage = "") {

      echo BuildTable($result, $colnames, $sortable, $pageadr, $tablename,
          $sortcol, $sortdir, $sortablecols, $selectlbl, $selectvar, $selectpage);

    }

  //print a nice table from a query result
  function BuildTable(PDOStatement $result, array $colnames = [], 
      Bool $sortable = false, String $pageadr = "", String $tablename = "t", 
      String $sortcol = "", String $sortdir = "ASC", array $sortablecols = [],
      String $selectlbl = "", String $selectvar = "", String $selectpage = "",
      Bool $numentry = false, String $entryvar = "", String $entrylbl = "", 
      Array $maxnums = [], &$price=null, &$weight=null, &$qtylist=null, $qty = false) {
    
    //column headers
    $tablestr = "<table border=1> <tr>";
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
        $tablestr .= "<th>$colstr</th>";
      } else {
        $selectcol = $c;
      }
    }
    if ($qtylist != "") {
      $tablestr .= "<th>Quantity</th>";
    }
    if($numentry && $entryvar != "") {
      $tablestr .= "<th>" . $entrylbl . "</th>";
    }
    if($selectlbl != "") {
      $tablestr .= "<th></th>";
    }
    $tablestr .= "</tr>";
    
    
    //read each row
    for($r = 0; $r < $result->rowCount(); $r++) {
      $row = $result->fetch();
      $tablestr .= "<tr>";
      for($c = 0; $c < $result->columnCount(); $c++) {
        if($c != $selectcol)
        $tablestr .= "<td>$row[$c]</td>";
      }
      if (!is_null($price) && !is_null($weight)) {
        $price += $row[2]*$qtylist[$r+1];
        $weight += $row[3]*$qtylist[$r+1];
      }

      if($numentry && $entryvar != "") {
        $tablestr .= "<td><input type=\"number\" name=\"" 
          . $entryvar . "_" . $row[$entryvar] 
          . "\" value=\"0\" step=\"1\" min=\"0\"";
        if(count($maxnums) > $r) {
          $tablestr .= " max=\"" . $maxnums[$r] . "\"";
        }
        $tablestr .= "></td>";
      }

      if($selectlbl != "" && $selectcol >= 0) {
        $tablestr .= "<td><a href=\"" . $selectpage . "?";
        if($selectpage == "") 
           $tablestr .= GetToString([$tablename . "_choice"]) ;
        $tablestr .= $tablename . "_choice=" . $row[$selectvar]
            . "\">" . $selectlbl . "</a></td>";
      }
      if($qty) {
        $tablestr .= "<td>".$qtylist[$r+1]."</td>";  
      }
      $tablestr .= "</tr>";
    }
    
    $tablestr .= "</table>";

    return $tablestr;
  }

  function BuildTableFromArray(array $array, array $colnames) {
    $tablestr = "<table border=1> <tr>";
    //$a = array_values($array);
    //$array = $a;

    if(count($array) > 0)
      $cols = count($array[0]);
    else
      $cols = 0;

    for($c = 0; $c < $cols; $c++) {
      $colname = "NO_NAME";
      if(count($colnames) > $c) {
        $colname = $colnames[$c];
      } 
      $tablestr .= "<th>$colname</th>";
    }
    $tablestr .= "</tr>";

    for($r = 0; $r < count($array); $r++) {
      $tablestr .= "<tr>";
      for($c = 0; $c < $cols; $c++) {
        $tablestr .= "<td>" . $array[$r][$c] . "</td>";
      }
      $tablestr .= "</tr>";
    } 
    $tablestr .= "</table>";
    $tablestr = preg_replace( "~(http://blitz.cs.niu.edu/pics/)(\S*.jpg)~", 
        "<img src=\"$1$2\" alt=\"\\2\" >",
        $tablestr);
    
    return $tablestr;
    
  }

  function MatchFirstElement(array $arr, $val) {
    foreach($arr as $r) {
      if(count($r) > 0 && $r[0] == $val)
        return $r;
    }
    return false;
  }

  function MergePartDetails(PDOStatement $result, array $partinfo) {
    $merged = [];
    for($r = 0; $r < $result->rowCount(); $r++) {
      $row = $result->fetch(PDO::FETCH_NUM);
      $partrow = MatchFirstElement($partinfo, $row[0]);
      $row[] = $partrow["description"];
      $row[] = $partrow["price"];
      $row[] = $partrow["weight"];
      $row[] = $partrow["pictureURL"];
      $merged[] = array_values($row);
    }
    return $merged;
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

  function FlattenArray(array $arr) {
    $newarr = [];
    foreach($arr as $e) {
      $newarr[$e[0]] = $e[1];
    }
    return $newarr;
  }

  
?>

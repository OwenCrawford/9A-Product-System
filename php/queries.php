<?php

    function OrderListQuery($sortcol, $sortdir) {
        return "SELECT Orders.orderNum,timePlaced,status,totalPrice FROM Orders
            ORDER BY $sortcol $sortdir;";
    }

    function OrderPackingListQuery($sortcol, $sortdir) {
        return "SELECT Orders.orderNum,timePlaced,status,totalPrice FROM Orders
            WHERE status = \"authorized\"
            ORDER BY $sortcol $sortdir;";
    }

    function UpdateOrderStatusQuery($orderNum, $status) {
        return "UPDATE Orders SET status = \"" . $status 
            . "\" WHERE orderNum = " . $orderNum . ";";
    }

    function PartListQuery($sortcol, $sortdir) {
        return "SELECT parts.number,description,price,weight,pictureURL FROM parts
            ORDER BY $sortcol $sortdir;";
    }

    function PartListSearchQuery($sortcol, $sortdir, $searchstr) {
        if($searchstr == "")
            return PartListQuery($sortcol, $sortdir);

        $searchstr = strtolower($searchstr);
        return "SELECT number,description,price,weight,pictureURL FROM parts
            WHERE number = '$searchstr' OR LOWER(description) LIKE '%$searchstr%'
            ORDER BY $sortcol $sortdir;";
    }

    function PartListNumericalSearchQuery($sortcol, $sortdir, array $searchnumlist) {
        return "SELECT number,description,price,weight,pictureURL FROM parts
            WHERE number IN (" . implode(',', $searchnumlist) . ") ORDER BY $sortcol $sortdir;";
    }


    function InventoryListQuery($partNum = -1) {
        if($partNum >= 0)
            return "SELECT * FROM Inventory WHERE partNum = $partNum;";
        else
            return "SELECT * FROM Inventory;";
    }

    function UpdatePartQuery($partNum, $quantity, Bool $new = false) {
        if($quantity == 0) 
            return "REMOVE FROM Inventory WHERE partnum = $partNum;";
        else if($new)
            return "INSERT INTO Inventory VALUES($partNum, $quantity);";
        else
            return "UPDATE Inventory SET quantity = $quantity
                        WHERE partNum = '$partNum';";
    }

    function OrderPartsListQuery($orderNum) {
        return "SELECT partNum FROM OrderParts
            WHERE orderNum = $orderNum;";
    }

    function OrderDetailQuery($orderNum) {
        return "SELECT partNum,quantity FROM OrderParts
            WHERE orderNum = $orderNum
            ORDER BY partNum;";
    }

    function PartDetailQuery(array $partNums) {
        return "SELECT number,description,price,weight,pictureURL FROM parts
            WHERE number IN (" . implode(',',$partNums) . ")
            ORDER BY number;";
    }
    
    function ShippingChargeQuery() {
        return "SELECT * FROM ShippingCharges ORDER BY weightCutoff;";
    }

    function ShippingChargeByWeightQuery($weight) {
        return "SELECT charge FROM ShippingCharges WHERE weightCutoff;";
    }
    function UpdateShippingQuery(array $upd) {
        return "UPDATE ShippingCharges 
            SET weightCutoff = " . $upd[1] . ", 
                charge = " . $upd[2]
            . "WHERE bracketName = \"" . $upd[0] . "\";";
    }

    function CustomerInfoQuery($orderNum) {
        return "SELECT custID,custName,email,custAddress FROM Orders 
            JOIN Customers ON Orders.customerID = Customers.custID
            WHERE Orders.orderNum = " . $orderNum . ";";
    }
?>

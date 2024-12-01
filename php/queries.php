<?php

    function OrderListQuery($sortcol, $sortdir) {
        return "SELECT Orders.orderNum,timePlaced,status,totalPrice FROM Orders
            ORDER BY $sortcol $sortdir;";
    }

    function PartListQuery($sortcol, $sortdir) {
        return "SELECT parts.number,description,price,weight,pictureURL FROM parts
            ORDER BY $sortcol $sortdir;";
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

    function UpdateShippingQuery(array $upd) {
        return "UPDATE ShippingCharges 
            SET weightCutoff = " . $upd[1] . ", 
                charge = " . $upd[2]
            . "WHERE bracketName = \"" . $upd[0] . "\";";
    }
?>
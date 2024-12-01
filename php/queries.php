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
        return "SELECT partNum FROM OrderPart
            WHERE orderNum = $orderNum;";
    }

    function OrderDetailQuery($orderNum) {
        return "SELECT partNum,quantity FROM OrderPart
            WHERE orderNum = $orderNum
            ORDER BY partNum;";
    }

    function PartDetailQuery(array $partNums) {
        return "SELECT number,description,price,weight,pictureURL FROM parts
            WHERE number IN $partnums
            ORDER BY number;";
    }
    


?>
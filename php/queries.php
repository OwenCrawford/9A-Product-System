<?php

    function OrderListQuery($sortcol, $sortdir) {
        return "SELECT Order.orderNum,timePlaced,status,totalPrice FROM Order
            ORDER BY $sortcol $sortdir;";
    }

?>
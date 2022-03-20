<?php
    require_once('../db.php');
    $data = array();
    $quantity = $_GET['quantity'];
    $q = mysqli_query($link, "SELECT * FROM `products` where id=$quantity");
    while ($row = mysqli_fetch_object($q)) {
        $data[] = $row;
    }
    echo json_encode($data[0]);
?>
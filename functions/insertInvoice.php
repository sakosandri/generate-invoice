<?php
require('../db.php');
if (isset($_POST['submit'])) {
    $street = $_POST['street'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $sql_addresses = "INSERT INTO `addresses` (line, city, state)
    VALUES ('$street ', '$city', '$state');";
    if ($link->multi_query($sql_addresses) === TRUE) {
        invoices($link->insert_id, $link);
    } else {
        echo "Error: " . $sql . "<br>" . $link->error;
    }
} else {
    header("Location: " . getHomeURL());
}
function invoices($last_address_id, $link)
{
    $invoice = $_POST['invoice'];
    $due_date = $_POST['dueDate'];
    $sql_invoices = "INSERT INTO `invoices`(`title`, `status`, `address_id`, `due_date`) VALUES ('$invoice','Open','$last_address_id','$due_date')";
    if ($link->multi_query($sql_invoices) === TRUE) {
        echo "New records created successfully " . $last_address_id;
        invoices_products($link->insert_id, $link);
    }
    $link->close();
}
function invoices_products($last_invoices_products_id, $link)
{
    $allProducts = json_decode($_POST["allProducts"]);
    for ($i = 0; $i < count($allProducts); $i++) {
        $quantity = $allProducts[$i]->quantity_in_stock;
        $product_id = $allProducts[$i]->id;
        $description = $_POST['description' . $i + 1];
        $invoices_products = "INSERT INTO `invoices_products`(`invoice_id`,`product_id`,`quantity`,`comment`) VALUES ('$last_invoices_products_id','$product_id','$quantity','$description')";
        if ($link->multi_query($invoices_products) === TRUE) {
            change_quantity($product_id, $quantity, $link);
            // echo "New records created successfully";
        }
    }
    echo "<script>confirm('You save successful you bill');</script>";
    // $link->close();
}
function change_quantity($product_id, $quantity, $link)

{
    $q = "UPDATE `products` SET quantity_in_stock=(SELECT quantity_in_stock-$quantity)  WHERE id='$product_id'";

    if ($link->query($q) === TRUE) {
        echo "<script>confirm('You save successful you bill');</script>";
    } else {
        echo "Error updating record: " . $link->error;
    }
    header("Location: " . getHomeURL());
    // $link->close();
}

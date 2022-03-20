<?php
function products()
{
    require_once('db.php');
    $data = array();
    $q = mysqli_query($link, "SELECT * FROM `products` where quantity_in_stock > 1");
    while ($row = mysqli_fetch_object($q)) {
        $data[] = $row;
    }
    return ($data);
}
?>

<div class="container mt-5 mb-3">
    <div class="row d-flex justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="d-flex flex-row p-2">
                    <div class="d-flex flex-column text-end">
                        <b>INVOICE</b>
                    </div>
                </div>
                <hr>
                <form method="POST" action="./functions/insertInvoice.php">
                    <div class="table-responsive p-2">
                        <div class="mb-3">
                            <label for="invoice" class="form-label">Invoice</label>
                            <input required type="text" class="form-control" id="invoice" name="invoice">
                        </div>
                        <div class="mb-3">
                            <label for="dueDate" class="form-label">Due Date</label>
                            <input required type="date" class="form-control" id="dueDate" name="dueDate">
                        </div>
                    </div>
                    <hr>
                    <div class="adress p-2">
                        <p><b>Address Information</b></p>
                        <div class="mb-3">
                            <label for="street" class="form-label">Street</label>
                            <input required type="text" class="form-control" id="street" name="street">
                        </div>
                        <div class="mb-3">
                            <label for="city" class="form-label">City</label>
                            <input required type="text" class="form-control" id="city" name="city">
                        </div>
                        <div class="mb-3">
                            <label for="state" class="form-label">State</label>
                            <input required type="text" class="form-control" id="state" name="state">
                        </div>

                    </div>
                    <div class="products p-2">
                        <p><b>Products</b></p>
                        <div class="row" id="products-show">
                        </div>
                        <button type="button" class="btn btn-success" onclick="showMultiProducts()">Add</button>
                        <h3 class="text-end"> Total: <input required readonly type="number" class="w-25" id="totalinvoice"> $</h3>

                    </div>
                    <input name="allProducts" id="allProducts" hidden />
                    <hr>
                    <div class="float-end p-2">
                        <button type="submit" class="btn btn-success text-center" name="submit" value="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    var count = 0;

    function showMultiProducts() {
        count++;
        $html_products = `<div class="row"> 
                            <div class="mb-3 col-4">
                                <label for="product` + count + `" class="form-label">Products</label>
                                <select type="text" class="form-control" id="product` + count + `" onchange="dataQuantity(` + count + `)" name="product` + count + `">
                                    <option></option>   
                                <?php foreach (products() as $products) : ?>
                                    <option value="<?= $products->id; ?>"><?= $products->name; ?></option>
                                <?php endforeach ?>
                                </select>
                            </div>
                            <div class="mb-3 col-4">
                                <label for="quantity` + count + `" class="form-label">Quantity</label>
                                <input required onchange="changeQuantity(` + count + `)" type="number" min="???" max="???" class="form-control" id="quantity` + count + `" name="quantity` + count + `">
                            </div>
                            <div class="mb-3 col-4">
                                <label for="description` + count + `" class="form-label">Description</label>
                                <input type="text" class="form-control" required id="description` + count + `" name="description` + count + `">
                            </div>
            </div>
                      `
        $('#products-show').append($html_products);
    }



    let saveProducts = [];

    function dataQuantity(id) {
        var selected = $('#product' + id).val();
        $.ajax({
            url: './functions/quantity.php',
            type: 'GET',
            dataType: 'json',
            data: {
                quantity: selected
            },
            success: function(resp) {
                saveProducts.push(resp);
                $('#quantity' + id).val(resp.quantity_in_stock);
                $('#quantity' + id).attr({
                    "max": saveProducts[Number(id) - 1].quantity_in_stock,
                    "min": 1
                });
                var sum = 0;
                for (let i = 0; i < saveProducts.length; i++) {
                    sum += (saveProducts[i].quantity_in_stock * saveProducts[i].unit_price)
                }
                $('#totalinvoice').val(sum);
                $('#allProducts').val(JSON.stringify(saveProducts));
            }
        });
    }

    function changeQuantity(id) {
        $('#quantity' + id).attr({
            "max": saveProducts[Number(id) - 1].quantity_in_stock,
            "min": 1
        });
        var sum = 0;
        saveProducts[Number(id) - 1].quantity_in_stock = $('#quantity' + id).val();
        for (let i = 0; i < saveProducts.length; i++) {
            sum += (saveProducts[i].quantity_in_stock * saveProducts[i].unit_price)
        }
        $('#totalinvoice').val(sum);
        $('#allProducts').val(JSON.stringify(saveProducts));
    }
</script>
<?php 
$total = 0;
$qry = $conn->query("SELECT c.*, p.name, i.price, p.id AS pid FROM `cart` c 
                      INNER JOIN `inventory` i ON i.id = c.inventory_id 
                      INNER JOIN products p ON p.id = i.product_id 
                      WHERE c.client_id = " . $_settings->userdata('id'));

while ($row = $qry->fetch_assoc()):
    $total += $row['price'] * $row['quantity'];
endwhile;
?>
<section class="py-5">
    <div class="container">
        <div class="card rounded-0">
            <div class="card-body"></div>
            <h3 class="text-center"><b>Checkout</b></h3>
            <hr class="border-dark">
            <form action="" id="place_order">
                <input type="hidden" name="amount" value="<?php echo $total ?>">
                <input type="hidden" name="payment_method" value="cod">
                <input type="hidden" name="paid" value="0">
                <div class="row row-col-1 justify-content-center">
                    <div class="col-6">
                        <div class="form-group col address-holder">
                            <label for="" class="control-label">Delivery Address</label>
                            <textarea id="" cols="30" rows="3" name="delivery_address" class="form-control" style="resize:none"><?php echo $_settings->userdata('default_delivery_address') ?></textarea>
                        </div>
                        <div class="col">
                            <span><h4><b>Total:</b> <?php echo number_format($total) ?></h4></span>
                        </div>
                        <hr>
                        <div class="col my-3">
                            <h4 class="text-muted">Payment Method</h4>
                            <div class="d-flex w-100 justify-content-between">
                                <button type="button" class="btn btn-flat btn-dark" id="cod-button">Cash on Delivery</button>
                                <button type="button" class="btn btn-flat" id="khalti-button">  <img src="uploads/payment/khalti.png" alt="Khalti" style="height: 50px;"/>
                                </button>
                                <button type="button" class="btn btn-flat" id="esewa-button">  <img src="uploads/payment/esewa.png" alt="Khalti" style="height: 50px;"/>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
$(function() {
    // Cash on Delivery button
    $('#cod-button').click(function() {
        $('[name="payment_method"]').val("Cash on Delivery");
        $('[name="paid"]').val(0);
        $('#place_order').submit();
    });

    // Khalti payment button
    $('#khalti-button').click(function() {
        var amount = '<?php echo $total; ?>';
        var config = {
            // Khalti configuration
            "publicKey": "test_public_key_XXXXXXXXXXXX",
            "amount": amount * 100, // Amount in paisa
            "transactionId": new Date().getTime(),
            "productIdentity": "product_id",
            "productName": "Order Payment",
            "mobile": "Your Mobile Number",
            "productUrl": "http://www.example.com",
            "eventHandler": {
                onSuccess: function (payload) {
                    // Handle successful payment here
                    payment_online(payload);
                },
                onError: function (error) {
                    console.error(error);
                    alert("Payment failed. Please try again.");
                },
                onClose: function () {
                    alert("Khalti payment dialog closed.");
                }
            }
        };
        KhaltiCheckout.show(config);
    });

    // eSewa payment button
    $('#esewa-button').click(function() {
        var amount = '<?php echo $total; ?>';
        // Redirect to eSewa payment gateway
        window.location.href = "https://esewa.com.np/epayment/main?amt=" + amount + "&scd=YOUR_SCD&pid=ORDER_ID";
    });

    $('#place_order').submit(function(e) {
        e.preventDefault();
        start_loader();
        $.ajax({
            url: 'classes/Master.php?f=place_order',
            method: 'POST',
            data: $(this).serialize(),
            dataType: "json",
            error: err => {
                console.log(err);
                alert_toast("An error occurred", "error");
                end_loader();
            },
            success: function(resp) {
                if (!!resp.status && resp.status == 'success') {
                    end_loader();
                    alert_toast("Order successfully placed", "success");
                    setTimeout(function() {
                        location.replace('./');
                    }, 2000);
                } else {
                    console.log(resp);
                    alert_toast("An error occurred", "error");
                    end_loader();
                }
            }
        });
    });
});

// Khalti payment initialization
var KhaltiCheckout = {
    show: function(config) {
        // Khalti checkout code (to be implemented based on Khalti's official documentation)
    }
};

function payment_online(paymentData) {
    $('[name="payment_method"]').val("Online Payment");
    $('[name="paid"]').val(1);
    $('#place_order').submit();
}
</script>

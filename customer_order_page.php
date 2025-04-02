<?php
session_start();
if (!isset($_SESSION['custID']) || !isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$custID = $_SESSION['custID'];
$email = $_SESSION['email'];

include('db.php');

$sql = "SELECT customerName FROM customers WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($customerName);
$stmt->fetch();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .item-card {
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin: 10px;
            width: 150px;
            cursor: pointer;
        }
        .item-card.selected {
            border-color: #007bff;
            background-color: #f0f8ff;
        }
        .quantity-control {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Place Your Order</h2>

        <!-- Display Customer Details -->
        <div class="mb-3">
            <label class="form-label">Customer ID</label>
            <input type="number" class="form-control" id="custID" name="custID" value="<?php echo $custID; ?>" readonly>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Customer Name</label>
            <input type="text" class="form-control" id="custName" value="<?php echo $customerName; ?>" readonly>
        </div>

        <h4>Select Items:</h4>
        <div id="itemsContainer" class="d-flex flex-wrap"></div>

        <h4 class="mt-3">Total Price: ₹ <span id="totalPrice">0</span></h4>

        <button type="submit" class="btn btn-primary mt-3" id="placeOrderBtn">Place Order</button>
    </div>

    <script>
        $(document).ready(function() {
            let selectedItems = {}; 

            $.ajax({
                url: 'fetch_items.php',
                type: 'GET',
                success: function(response) {
                    const items = JSON.parse(response);
                    items.forEach(item => {
                        $('#itemsContainer').append(`
                            <div class="item-card" data-id="${item.itemID}" data-price="${item.price}">
                                <h5>${item.itemName}</h5>
                                <p>₹ ${item.price}</p>
                                <div class="quantity-control">
                                    <button class="btn btn-sm btn-danger decrease" disabled>-</button>
                                    <span class="quantity">0</span>
                                    <button class="btn btn-sm btn-success increase">+</button>
                                </div>
                            </div>
                        `);
                    });
                }
            });

            // Handle quantity increase/decrease
            $(document).on('click', '.increase', function() {
                let card = $(this).closest('.item-card');
                let itemID = card.data('id');
                let price = parseFloat(card.data('price'));

                // Update quantity
                let quantityElem = card.find('.quantity');
                let quantity = parseInt(quantityElem.text()) + 1;
                quantityElem.text(quantity);

                // Enable the decrease button
                card.find('.decrease').prop('disabled', false);

                // Update selectedItems object
                selectedItems[itemID] = { price: price, quantity: quantity };

                updateTotalPrice();
            });

            $(document).on('click', '.decrease', function() {
                let card = $(this).closest('.item-card');
                let itemID = card.data('id');

                let quantityElem = card.find('.quantity');
                let quantity = parseInt(quantityElem.text()) - 1;

                if (quantity === 0) {
                    delete selectedItems[itemID]; 
                    card.find('.decrease').prop('disabled', true);
                } else {
                    selectedItems[itemID].quantity = quantity;
                }

                quantityElem.text(quantity);
                updateTotalPrice();
            });

            function updateTotalPrice() {
                let total = 0;
                Object.values(selectedItems).forEach(item => {
                    total += item.price * item.quantity;
                });
                $('#totalPrice').text(total.toFixed(2));
            }

            // Handle order form submission
            $('#placeOrderBtn').click(function(e) {
                e.preventDefault();

                let custID = $('#custID').val();

                if (Object.keys(selectedItems).length === 0) {
                    alert('Please select at least one item.');
                    return;
                }

                $.ajax({
                    url: 'insert_order.php',
                    type: 'POST',
                    data: {
                        custID: custID,
                        items: selectedItems
                    },
                    success: function(response) {
                        if (response === 'success') {
                            alert('Order placed successfully');
                            $('#orderForm')[0].reset();
                            $('#itemsContainer').empty();
                            $('#totalPrice').text('0');
                            selectedItems = {};
                        } else {
                            alert('Error: ' + response);
                        }
                    }
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


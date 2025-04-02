<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Insert Item</h2>
        <form id="insertItemForm" method="POST">
            <div class="mb-3">
                <label for="itemName" class="form-label">Item Name</label>
                <input type="text" class="form-control" id="itemName" name="itemName">
                <div id="itemNameError" class="text-danger"></div>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price">
                <div id="priceError" class="text-danger"></div>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity">
                <div id="quantityError" class="text-danger"></div>
            </div>
            <div class="mb-3">
                <label for="availability" class="form-label">Availability</label>
                <select class="form-control" id="availability" name="availability">
                    <option value="1">In Stock</option>
                    <option value="0">Out of Stock</option>
                </select>
                <div id="availabilityError" class="text-danger"></div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('#insertItemForm').submit(function(e) {
                e.preventDefault();

                $('.text-danger').text('');

                let valid = true;

                const itemName = $('#itemName').val();
                const price = $('#price').val();
                const quantity = $('#quantity').val();
                const availability = $('#availability').val();

                if (itemName.trim() === '') {
                    $('#itemNameError').text('Item Name is');
                    valid = false;
                }
                if (price <= 0 || price === '') {
                    $('#priceError').text('Price should be a positive decimal number');
                    valid = false;
                }
                if (quantity <= 0 || quantity === '') {
                    $('#quantityError').text('Quantity should be a positive number');
                    valid = false;
                }
                if (availability === '') {
                    $('#availabilityError').text('Availability is');
                    valid = false;
                }

                if (valid) {
                    // Send data to the backend via AJAX
                    $.ajax({
                        url: 'insert_item.php',
                        type: 'POST',
                        data: {
                            itemName: itemName,
                            price: price,
                            quantity: quantity,
                            availability: availability
                        },
                        success: function(response) {
                            if (response === 'success') {
                                alert('Item inserted successfully');
                                $('#insertItemForm')[0].reset();
                            } else {
                                alert('Error: ' + response);
                            }
                        }
                    });
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

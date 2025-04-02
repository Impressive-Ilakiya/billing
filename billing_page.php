<?php
include('db.php');

// Get the last bill number from the database and increment it
$result = $conn->query("SELECT MAX(bill_no) AS last_bill FROM bills");
$row = $result->fetch_assoc();
$next_bill_no = $row['last_bill'] + 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Billing</h2>

        <form id="billingForm" method="POST" action="process_bill.php">
            <div class="mb-3">
                <label class="form-label"><strong>Bill No.</strong></label>
                <input type="text" name="bill_no" class="form-control" readonly value="<?php echo $next_bill_no; ?>">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Customer</strong></label>
                <select name="customer" class="form-control" id="custSelect">
                    <option value="">Select Customer</option>
                    <?php
                    $customers = $conn->query("SELECT custID, custName FROM customers");
                    while ($row = $customers->fetch_assoc()) {
                        echo "<option value='" . $row['custID'] . "'>" . $row['custName'] . "</option>";
                    }
                    ?>
                </select>
                <div class="text-danger" id="custError"></div>
            </div>
            <div class="mb-3">
                <button type="button" class="btn btn-primary" id="showCustomerForm">Add Customer</button>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="col-3">Item</th>
                        <th class="col-2">Price</th>
                        <th class="col-2">GST %</th>
                        <th class="col-2">Quantity</th>
                        <th class="col-2">Total Price</th>
                        <th class="col-1">Action</th>
                    </tr>
                </thead>
                <tbody id="billingTable">
                    <tr>
                        <td>
                            <select name="item[]" class="form-control item-select">
                                <option value="">Select Item</option>
                                <?php
                                $items = $conn->query("SELECT itemID, itemName, price FROM items");
                                while ($row = $items->fetch_assoc()) {
                                    echo "<option value='" . $row['itemID'] . "' data-price='" . $row['price'] . "'>" . $row['itemName'] . "</option>";
                                }
                                ?>
                            </select>
                            <div class="text-danger" id="itemError"></div>
                        </td>
                        <td><input type="number" name="price[]" class="form-control price-input" value="0.00" step="0.01" min="0"></td>
                        <td><input type="number" name="gst[]" class="form-control gst-input" value="0.00" min="0.00"></td>
                        <td class="d-flex">
                            <button type="button" class="btn btn-danger btn-sm decrement">-</button>
                            <input type="number" name="quantity[]" class="form-control quantity text-center" value="1" min="1">
                            <button type="button" class="btn btn-success btn-sm increment">+</button>
                        </td>
                        <td><input type="text" name="total_price[]" class="form-control total-price" value="0.00" readonly></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                    </tr>
                </tbody>
            </table>
            <h4 class="mt-3">Total Amount: ₹<span id="totalAmount">0.00</span></h4>
            <button type="button" class="btn btn-success" id="addItemRow">Add Item</button>
            <button type="submit" class="btn btn-primary">Submit Bill</button>
        </form>
    </div>

    <div class="modal fade" id="customerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function updateTotalPrice(row) {
                let price = parseFloat(row.find(".price-input").val()) || 0;
                let quantity = parseInt(row.find(".quantity").val()) || 1;
                let gst = parseFloat(row.find(".gst-input").val()) || 0;

                // 🛠 Fix: Ensure GST is applied correctly
                let total = (price * quantity) + ((price * quantity) * (gst / 100));

                row.find(".total-price").val(total.toFixed(2));
                updateTotalAmount();
            }



            function updateTotalAmount() {
                let totalAmount = 0;
                $(".total-price").each(function() {
                    totalAmount += parseFloat($(this).val()) || 0;
                });
                $("#totalAmount").text(totalAmount.toFixed(2));
            }

            $("#addItemRow").click(function() {
                var newRow = $("#billingTable tr:first").clone();
                newRow.find("input").val(''); // Clear input values
                newRow.find(".total-price").val(''); // Clear total price
                $("#billingTable").append(newRow);
            });

            // Remove an item row
            $(document).on("click", ".remove-row", function() {
                if ($("#billingTable tr").length > 1) { // Prevent removing the last row
                    $(this).closest("tr").remove();
                    updateTotalAmount();
                }
            });

            $(document).on("change", ".item-select", function() {
                let row = $(this).closest("tr");
                let price = parseFloat($(this).find("option:selected").data("price")) || 0;
                row.find(".price-input").val(price.toFixed(2));
                updateTotalPrice(row);
            });

            $(document).on("input", ".price-input", function() {
                let row = $(this).closest("tr");
                updateTotalPrice(row);
            });

            $(document).on("click", ".increment, .decrement", function() {
                let row = $(this).closest("tr");
                updateTotalPrice(row);
            });

            $(document).on("input", ".gst-input", function() {
                let row = $(this).closest("tr");
                updateTotalPrice(row);
            });



            $(document).on("click", ".increment", function() {
                let row = $(this).closest("tr");
                let quantity = parseInt(row.find(".quantity").val()) + 1;
                row.find(".quantity").val(quantity);
                updateTotalPrice(row);
            });

            $(document).on("click", ".decrement", function() {
                let row = $(this).closest("tr");
                let quantity = parseInt(row.find(".quantity").val()) - 1;
                if (quantity >= 1) {
                    row.find(".quantity").val(quantity);
                    updateTotalPrice(row);
                }
            });

            $(document).on("input", ".gst-input", function() {
                let row = $(this).closest("tr");
                updateTotalPrice(row);
            });

            $("#showCustomerForm").click(function() {
                $.ajax({
                    url: "customer_details.php",
                    type: "GET",
                    success: function(response) {
                        $("#customerModal .modal-body").html(response);
                        $("#customerModal").modal("show");
                    },
                    error: function() {
                        alert("Failed to load customer details.");
                    }
                });
            });
        });

        // jQuery Validation before submission
        $("#billingForm").submit(function(e) {
            let valid = true;

            if ($("#custSelect").val() === "") {
                $("#custError").html("Please select a customer!");
                valid = false;
            }

            $(".item-select").each(function() {
                if ($(this).val() === "") {
                    $("#itemError").html("Please select an item!");
                    valid = false;
                    return false;
                }
            });

            if (!valid) e.preventDefault();
        });
    </script>
</body>

</html>
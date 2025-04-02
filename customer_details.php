<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2>Insert Customer Details</h2>
        <form id="insertCustomerForm" method="POST">
            <div class="mb-3">
                <label for="custName" class="form-label">Customer Name</label>
                <input type="text" class="form-control" id="custName" name="custName">
                <div id="custNameError" class="text-danger"></div>
            </div>
            <div class="mb-3">
                <label for="custMobile" class="form-label">Customer Mobile</label>
                <input type="text" class="form-control" id="custMobile" name="custMobile">
                <div id="custMobileError" class="text-danger"></div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email">
                <div id="emailError" class="text-danger"></div>
            </div>
            <!-- <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password">
                <div id="passwordError" class="text-danger"></div>
            </div> -->
            <div class="mb-3">
                <label for="country" class="form-label">Country</label>
                <input type="text" class="form-control" id="country" name="country">
                <div id="countryError" class="text-danger"></div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Customer added successfully!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $('#insertCustomerForm').submit(function(e) {
                e.preventDefault();

                $('.text-danger').text('');

                let valid = true;

                const custName = $('#custName').val();
                const custMobile = $('#custMobile').val();
                const email = $('#email').val();
                const country = $('#country').val();
                const password = $('#password').val();

                if (custName.trim() === '') {
                    $('#custNameError').text('Customer Name is required');
                    valid = false;
                }
                if (custMobile.trim() === '' || !/^\+?[0-9]{10,15}$/.test(custMobile)) {
                    $('#custMobileError').text('Invalid Mobile Number');
                    valid = false;
                }
                if (email.trim() === '' || !/\S+@\S+\.\S+/.test(email)) {
                    $('#emailError').text('Invalid Email');
                    valid = false;
                }
                // if (password.trim() === '') {
                //     $('#passwordError').text('Password is required');
                //     valid = false;
                // }
                if (country.trim() === '') {
                    $('#countryError').text('Country is required');
                    valid = false;
                }

                if (valid) {
                    $.ajax({
                        url: 'insert_customer.php',
                        type: 'POST',
                        data: {
                            custName: custName,
                            custMobile: custMobile,
                            email: email,
                            // password: password,
                            country: country
                        },
                        success: function(response) {
                            if (response === 'success') {
                                $('#insertCustomerForm')[0].reset();
                                $("#customerFormContainer").html(''); // Hide the form after submission

                                // Show Bootstrap modal instead of alert
                                $("#successModal").modal('show');

                                // Fetch and update customer dropdown in billing_page.php
                                $.ajax({
                                    url: 'fetch_customers.php', // A new file to fetch updated customers
                                    type: 'GET',
                                    success: function(data) {
                                        $("#custSelect").html(data); // Replace old dropdown options with new ones
                                    }
                                });
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
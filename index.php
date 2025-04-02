<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2>Login</h2>
        <form id="loginForm" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
                <div id="emailError" class="text-danger"></div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
                <div id="passwordError" class="text-danger"></div>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            $('#loginForm').submit(function (e) {
                e.preventDefault();

                    $('#emailError').text('');
                $('#passwordError').text('');

                let valid = true;

                const email = $('#email').val();
                const password = $('#password').val();

                if (email.trim() === '' || !/\S+@\S+\.\S+/.test(email)) {
                    $('#emailError').text('Invalid Email Address');
                    valid = false;
                }

                if (password.trim() === '') {
                    $('#passwordError').text('Password is required');
                    valid = false;
                }

                if (valid) {
                    $.ajax({
                        url: 'login.php',
                        type: 'POST',
                        data: {
                            email: email,
                            password: password
                        },
                        success: function (response) {
                            if (response === 'success') {
                                alert('Login successful!');
                                window.location.href = 'customer_order_page.php';
                            } else {
                                alert(response);
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

<?php
include('db.php');

$custName = $_POST['custName'];
$custMobile = $_POST['custMobile'];
$email = $_POST['email'];
$country = $_POST['country'];
// $password = $_POST['password'];
// $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

if (empty($custName) || empty($custMobile) || empty($email) || empty($country)) {
    echo "All fields are required.";
    exit();
}

$sql = "INSERT INTO customers (custName, custMobile, email, country) VALUES ('$custName', '$custMobile', '$email', '$country')";

if ($conn->query($sql) === TRUE) {
    echo "success";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();

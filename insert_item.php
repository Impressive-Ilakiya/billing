<?php
include('db.php');


$itemName = $_POST['itemName'];
$price = $_POST['price'];
$quantity = $_POST['quantity'];
$availability = $_POST['availability'];

if (empty($itemName) || empty($price) || empty($quantity) || !isset($availability)) {
    echo "All fields are required.";
    exit();
}

$sql = "INSERT INTO items (itemName, price, quantity, availability) VALUES ('$itemName', '$price', '$quantity', '$availability')";

if ($conn->query($sql) === TRUE) {
    echo "success";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>

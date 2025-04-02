<?php
include 'db.php';
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $custID = $_SESSION['custID'];
    $items = json_decode($_POST['items'], true);

    foreach ($items as $itemID => $item) {
        $quantity = $item['quantity'];
        $price = $item['price'];
        $totalPrice = $price * $quantity;

        $sql = "INSERT INTO orders (custID, itemID, quantity, totalPrice) 
                VALUES ('$custID', '$itemID', '$quantity', '$totalPrice')";

        if (!$conn->query($sql)) {
            echo "Error: " . $conn->error;
            exit;
        }
    }

    echo "success";
}
?>

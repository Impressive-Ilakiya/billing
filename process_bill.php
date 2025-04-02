<?php
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bill_no = $_POST['bill_no'];
    $customer_id = $_POST['customer'];
    $items = $_POST['item'];
    $quantities = $_POST['quantity'];
    $total_prices = $_POST['total_price'];
    $gst = $_POST['gst'];


    // Calculate total amount for the bill
    $total_amount = array_sum($total_prices);

    // Insert the bill into the `bills` table
    $stmt = $conn->prepare("INSERT INTO bills (bill_no, customer_id, total_amount) VALUES (?, ?, ?)");
    $stmt->bind_param("iid", $bill_no, $customer_id, $total_amount);
    $stmt->execute();
    $stmt->close();

    // Prepare statement for inserting into `bill_items`
    $stmt = $conn->prepare("INSERT INTO bill_items (bill_no, item_id, quantity, price, gst, total_price) VALUES (?, ?, ?, ?, ?, ?)");

    for ($i = 0; $i < count($items); $i++) {
        $item_id = $items[$i];
        $quantity = $quantities[$i];
        $price = $_POST['price'][$i];
        $gst = $_POST['gst'][$i];
        $total_price = $total_prices[$i];


        // ✅ Fetch the available quantity from the database
        $fetch_stmt = $conn->prepare("SELECT quantity FROM items WHERE itemID = ?");
        $fetch_stmt->bind_param("i", $item_id);
        $fetch_stmt->execute();
        $result = $fetch_stmt->get_result();
        $row = $result->fetch_assoc();
        $fetch_stmt->close();

        if (!$row) {
            echo "<script>alert('Error: Item not found!'); window.location.href='billing_page.php';</script>";
            exit();
        }

        $available_qty = $row['quantity'];

        // ✅ Check stock availability
        if ($available_qty < $quantity) {
            echo "<script>alert('Error: Not enough stock for item ID $item_id!'); window.location.href='billing_page.php';</script>";
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO bill_items (bill_no, item_id, quantity, price, gst, total_price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiddd", $bill_no, $item_id, $quantity, $price, $gst, $total_price);
        $stmt->execute();

        $update_stmt = $conn->prepare("UPDATE items SET quantity = quantity - ? WHERE itemID = ?");
        $update_stmt->bind_param("ii", $quantity, $item_id);
        $update_stmt->execute();
        $update_stmt->close();
    }



    $stmt->close();
    $conn->close();

    echo "<script>alert('Bill Submitted Successfully!'); window.location.href='billing_page.php';</script>";
}

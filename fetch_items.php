<?php
include 'db.php';

$sql = "SELECT itemID, itemName, price FROM items";
$result = $conn->query($sql);

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items);
?>

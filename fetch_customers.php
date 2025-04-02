<?php
include('db.php');

echo '<option value="">Select Customer</option>';
$customers = $conn->query("SELECT custID, custName FROM customers");
while ($row = $customers->fetch_assoc()) {
    echo "<option value='" . $row['custID'] . "'>" . $row['custName'] . "</option>";
}
?>

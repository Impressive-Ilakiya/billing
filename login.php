<?php
session_start();

include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT password FROM customers WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($storedHashedPassword);
    $stmt->fetch();

    echo "Entered Password: " . $password . "<br>";
    echo "Stored Hash from DB: " . $storedHashedPassword . "<br>";

    if (password_verify($password, $storedHashedPassword)) {
        $_SESSION['email'] = $email;
        echo "success";
    } else {
        echo "Invalid credentials";
    }

    $stmt->close();
    $conn->close();
}


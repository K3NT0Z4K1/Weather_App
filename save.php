<?php
// save.php
// Place this file in: C:\xampp\htdocs\weather\save.php

$host   = "localhost";
$user   = "root";
$pass   = "";           // XAMPP default has no password
$dbname = "weather_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tempC    = floatval($_POST["tempC"]);
    $tempF    = floatval($_POST["tempF"]);
    $humidity = floatval($_POST["humidity"]);

    $stmt = $conn->prepare("INSERT INTO readings (tempC, tempF, humidity) VALUES (?, ?, ?)");
    $stmt->bind_param("ddd", $tempC, $tempF, $humidity);

    if ($stmt->execute()) {
        http_response_code(200);
        echo "OK";
    } else {
        http_response_code(500);
        echo "Insert failed";
    }

    $stmt->close();
} else {
    http_response_code(405);
    echo "Method not allowed";
}

$conn->close();
?>
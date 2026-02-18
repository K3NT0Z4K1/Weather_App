<?php
// fetch.php
// Place this file in: C:\xampp\htdocs\weather\fetch.php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "weather_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed"]);
    exit;
}

// Get last 50 readings for the graph
$result = $conn->query("SELECT tempC, tempF, humidity, recorded_at FROM readings ORDER BY recorded_at DESC LIMIT 50");

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

// Reverse so oldest is first (for graph left-to-right order)
$rows = array_reverse($rows);

echo json_encode($rows);
$conn->close();
?>

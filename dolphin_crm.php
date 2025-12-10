<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'dolphin_crm';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
?>

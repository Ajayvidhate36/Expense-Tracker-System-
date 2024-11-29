<?php
// Database connection settings
$host = 'localhost'; // Replace with your database host
$dbname = 'expense_tracker'; // The database name
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

try {
    // Create a PDO instance to connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Display an error message if connection fails
    die("Connection failed: " . $e->getMessage());
}
?>

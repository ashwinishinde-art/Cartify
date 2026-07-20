<?php
// db.php
$host = 'localhost';
$dbname = 'cartify_db';
$username = 'root'; // Default XAMPP/MAMP username
$password = '';     // Default XAMPP password (MAMP is usually 'root')

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set error mode to exception to catch issues early
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
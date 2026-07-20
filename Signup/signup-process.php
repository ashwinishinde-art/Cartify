<?php
// Signup/signup-process.php
require_once '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        die("All fields are required.");
    }

    // 2. Securely hash the password (Never store plain text!)
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        // 3. Check if the email already exists
        $checkEmail = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkEmail->execute([$email]);
        
        if ($checkEmail->rowCount() > 0) {
            die("Email is already registered.");
        }

        // 4. Insert the new user
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $email, $hashedPassword])) {
            // Success! Send them to the login page
            header("Location: ../Login/index.html");
            exit;
        }
    } catch (PDOException $e) {
        die("Error creating account: " . $e->getMessage());
    }
}
?>
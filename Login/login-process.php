<?php
// Enable error reporting for testing
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Point to your centralized db.php in the root directory
require_once '../db.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        die("Please fill in all fields.");
    }

    try {
        // Fetch the user by email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            // Save key details to Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role']; // Will be 'buyer' or 'merchant'

            // --- SMART ROUTING SYSTEM ---
            if ($user['role'] === 'merchant') {
                // If they are a registered merchant, send them straight to their dashboard
                header("Location: ../Merchant/dashboard.php");
            } else {
                // If they are a standard shopper, send them to the homepage
                header("Location: ../index.php");
            }
            exit;
            
        } else {
            die("Invalid email or password.");
        }

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    header("Location: index.html");
    exit;
}
?>
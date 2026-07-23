<?php
// Enable error reporting for testing
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../db.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Capture custom redirect parameter if passed
    $redirect_to = trim($_POST['redirect_to'] ?? $_GET['redirect_to'] ?? '');

    if (empty($email) || empty($password)) {
        $err_url = "index.php?error=empty_fields";
        if (!empty($redirect_to)) {
            $err_url .= "&redirect_to=" . urlencode($redirect_to);
        }
        header("Location: " . $err_url);
        exit;
    }

    try {
        // Fetch user by email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role']; 

            // Smart Routing
            if ($user['role'] === 'merchant') {
                header("Location: ../Merchant/dashboard.php");
            } else {
                // If a specific redirect path is set, send user directly there
                if (!empty($redirect_to)) {
                    header("Location: " . $redirect_to);
                } else {
                    header("Location: ../index.php");
                }
            }
            exit;
            
        } else {
            // REDIRECT BACK WITH ERROR PARAMETER & KEEP REDIRECT TARGET
            $err_url = "index.php?error=invalid_credentials";
            if (!empty($redirect_to)) {
                $err_url .= "&redirect_to=" . urlencode($redirect_to);
            }
            header("Location: " . $err_url);
            exit;
        }

    } catch (PDOException $e) {
        $err_url = "index.php?error=db_error";
        if (!empty($redirect_to)) {
            $err_url .= "&redirect_to=" . urlencode($redirect_to);
        }
        header("Location: " . $err_url);
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>
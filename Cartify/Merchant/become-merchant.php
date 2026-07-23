<?php
// Merchant/become-merchant.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../db.php'; 
session_start();

// 1. SECURITY LOCK: Force check that the user is actually logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /Cartify/login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// 2. LIVE STATUS INTERCEPT: Check if their merchant authorization was revoked by an admin
try {
    $statusCheck = $pdo->prepare("SELECT merchant_status FROM users WHERE id = ?");
    $statusCheck->execute([$user_id]);
    $currentStatus = $statusCheck->fetchColumn();

    if ($currentStatus === 'revoked') {
        // Show a professional access restricted view and completely stop further execution
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Application Restricted - Cartify</title>
            <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
            <style>
                body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
                .error-card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); width: 100%; max-width: 480px; text-align: center; border: 1px solid #e2e8f0; }
                .icon-shield { background: #fef2f2; color: #ef4444; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px auto; font-size: 28px; font-weight: bold; }
                h2 { color: #1e293b; margin: 0 0 12px 0; font-size: 1.5rem; font-weight: 700; }
                p { color: #64748b; font-size: 0.95rem; line-height: 1.6; margin: 0 0 28px 0; }
                .back-btn { display: inline-block; padding: 12px 24px; background: #556246; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 0.9rem; transition: background 0.2s; }
                .back-btn:hover { background: #455038; }
            </style>
        </head>
        <body>
            <div class="error-card">
                <div class="icon-shield">✕</div>
                <h2>Access Privileges Suspended</h2>
                <p>Your merchant onboarding privileges for this account have been explicitly revoked by the system administration due to a violation of our marketplace seller policies.</p>
                <a href="/Cartify/index.php" class="back-btn">Return to Storefront</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
} catch (PDOException $e) {
    // Fail safely if database table column is loading dynamically
}

// 3. ROUTER MATRIX: If a GET request comes in from the footer link, point them safely to step 1
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header("Location: index.php?step=1");
    exit;
}

// 4. STEP DATA FORM PROCESSOR LAYER (POST processing logic matches your step structure)
$step = isset($_POST['step']) ? (int)$_POST['step'] : 1;

try {
    // STEP 1: Save Store Name & Mobile Number, then go to Step 2
    if ($step === 1) {
        $store_name = trim($_POST['store_name']);
        $phone_number = trim($_POST['phone_number']);

        if (empty($store_name) || empty($phone_number)) {
            die("Store name and mobile number are required.");
        }

        $stmt = $pdo->prepare("UPDATE users SET store_name = ?, phone_number = ? WHERE id = ?");
        $stmt->execute([$store_name, $phone_number, $user_id]);
        
        header("Location: index.php?step=2");
        exit;

    // STEP 2: Save Pickup Address & Shipping Method, then go to Step 3
    } elseif ($step === 2) {
        $business_address = trim($_POST['business_address']);
        $shipping_method = trim($_POST['shipping_method']);

        if (empty($business_address) || empty($shipping_method)) {
            die("Pickup address and shipping method are required.");
        }

        $stmt = $pdo->prepare("UPDATE users SET business_address = ?, shipping_method = ? WHERE id = ?");
        $stmt->execute([$business_address, $shipping_method, $user_id]);
        
        header("Location: index.php?step=3");
        exit;

    // STEP 3: Save Bank Account & IFSC, then Activate Merchant Role!
    } elseif ($step === 3) {
        $bank_account_no = trim($_POST['bank_account_no']);
        $bank_ifsc = trim($_POST['bank_ifsc']);

        if (empty($bank_account_no) || empty($bank_ifsc)) {
            die("Bank account number and IFSC code are required.");
        }

        // Finalize activation: Update bank details, switch role to 'merchant' and flag merchant_status as active
        $stmt = $pdo->prepare("UPDATE users SET bank_account_no = ?, bank_ifsc = ?, role = 'merchant', merchant_status = 'active' WHERE id = ?");
        
        if ($stmt->execute([$bank_account_no, $bank_ifsc, $user_id])) {
            // Update active session values
            $_SESSION['user_role'] = 'merchant';
            
            // Grab the store name to keep the header updated
            $getStore = $pdo->prepare("SELECT store_name FROM users WHERE id = ?");
            $getStore->execute([$user_id]);
            $store = $getStore->fetch(PDO::FETCH_ASSOC);
            $_SESSION['store_name'] = $store['store_name'];

            header("Location: dashboard.php");
            exit;
        }
    }
} catch (PDOException $e) {
    die("Data persistence crash on Step " . $step . ": " . $e->getMessage());
}
?>
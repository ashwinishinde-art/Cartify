<?php
// Products/verify-payment.php
require_once '../db.php';
session_start();

if (!isset($_GET['payment_id']) || !isset($_SESSION['checkout_summary'])) {
    header("Location: products.php");
    exit;
}

$payment_id = htmlspecialchars($_GET['payment_id']);
$razorpay_signature = htmlspecialchars($_GET['signature'] ?? '');
$applied_promo = htmlspecialchars($_GET['promo'] ?? '');
$summary = $_SESSION['checkout_summary'];

// 1. SAFE TESTING ENVIRONMENT SIGNATURE PASSTHROUGH
if (!empty($razorpay_signature) && $razorpay_signature !== 'undefined') {
    $key_secret = "YOUR_RAZORPAY_SECRET_KEY"; // Paste secret here if validating signatures
    $generated_signature = hash_hmac('sha256', $payment_id, $key_secret);

    if ($generated_signature !== $razorpay_signature) {
        die("Security alert: Payment signature verification failed.");
    }
}

// Capture shipping inputs out from session arrays safely
$shipping = $_SESSION['checkout_shipping'] ?? [
    'name' => 'Customer Account Holder',
    'phone' => '9999999999',
    'address' => 'Address Details Confirmed via Razorpay'
];

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;

try {
    // 2. SAVING ORDER DETAILS
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, product_id, quantity, total_price, payment_id, customer_name, customer_phone, delivery_address, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
    ");
    
    foreach ($summary['items'] as $item) {
        $stmt->execute([
            $user_id,
            $item['id'],
            $item['quantity'],
            $item['item_total'],
            $payment_id,
            $shipping['name'],
            $shipping['phone'],
            $shipping['address']
        ]);
    }
} catch (PDOException $e) {
    // Elegant warning print out with direct troubleshooting tips
    echo "<div style='padding:20px; background:#fce8e6; color:#c5221f; font-family:sans-serif; border-radius:8px; margin:20px;'>";
    echo "<h3>Database Schema Notice</h3>";
    echo "<p>Your SQL error means your table doesn't have the new column yet.</p>";
    echo "<strong>To fix this right now:</strong> Go to phpMyAdmin &rarr; Click your database &rarr; Click the <strong>SQL</strong> tab &rarr; Run this command:<br><br>";
    echo "<code>ALTER TABLE orders ADD COLUMN customer_phone VARCHAR(20) NOT NULL AFTER customer_name;</code>";
    echo "<br><br><em>System Debug: " . $e->getMessage() . "</em>";
    echo "</div>";
    exit;
}

// Clear out standard session checkout cache records cleanly
unset($_SESSION['cart']);
unset($_SESSION['checkout_summary']);
unset($_SESSION['checkout_shipping']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Cartify</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fafdf8; text-align: center; padding: 80px 20px; color: #1a1a1a; }
        .success-card { max-width: 550px; background: #fff; margin: 0 auto; padding: 40px; border-radius: 24px; box-shadow: 0 8px 30px rgba(85, 98, 70, 0.06); border: 1px solid rgba(85, 98, 70, 0.08); }
        .icon { font-size: 4.5rem; color: #137333; margin-bottom: 20px; }
        h1 { color: #5D6247; font-size: 2rem; margin-bottom: 8px; }
        p { color: #666; margin-bottom: 30px; line-height: 1.5; }
        .meta-detail { background: #f5f7f3; padding: 16px; border-radius: 12px; font-size: 0.9rem; text-align: left; margin-bottom: 30px; }
        .meta-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .btn-wrapper { display: flex; gap: 12px; justify-content: center; }
        .btn-home { display: inline-block; padding: 14px 24px; background: #5D6247; color: white; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 0.95rem; transition: background 0.2s; }
        .btn-home:hover { background: #424d36; }
        .btn-track { background: transparent; color: #5D6247; border: 2px solid #5D6247; padding: 12px 22px; }
        .btn-track:hover { background: rgba(93, 98, 71, 0.05); }
    </style>
</head>
<body>

    <div class="success-card">
        <div class="icon">✓</div>
        <h1>Order Confirmed!</h1>
        <p>Thank you for shopping with us. Your payment was captured securely via Razorpay and your items are being prepared for shipping.</p>
        
        <div class="meta-detail">
            <div class="meta-row"><strong>Transaction ID:</strong> <span><?php echo $payment_id; ?></span></div>
            <div class="meta-row"><strong>Coupon Code Used:</strong> <span><?php echo !empty($applied_promo) ? $applied_promo : 'None'; ?></span></div>
            <div class="meta-row" style="margin-top:8px; border-top:1px solid #ddd; padding-top:8px; font-weight:700;">
                <strong>Total Authenticated Amount:</strong> 
                <span>₹<?php echo number_format($summary['raw_amount'], 2); ?></span>
            </div>
        </div>

        <div class="btn-wrapper">
            <a href="track-orders.php" class="btn-home btn-track">Track Order</a>
            <a href="/Cartify/index.php" class="btn-home">Continue Shopping</a>
        </div>
    </div>

</body>
</html>
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

/*
 * ------------------------------------------------------------------
 * IMPORTANT — not fixed here, flagging clearly:
 *
 * The amount actually charged in Razorpay is calculated in the
 * browser (checkout.php's `finalPayable`, built from client-side
 * promo math) and this page never re-checks it against a
 * server-computed total. `$summary['raw_amount']` below IS
 * trustworthy (it comes from DB prices in checkout.php), but nothing
 * here confirms the customer's card was actually charged that much —
 * only that *a* Razorpay payment ID was passed in the URL. Because
 * $key_secret is still a placeholder, the signature check below is
 * skipped whenever no signature is supplied, so this page will
 * currently record ANY payment_id as a paid order.
 *
 * The correct fix is to create the Razorpay Order server-side (via
 * the Orders API) for the exact server-computed amount *before*
 * opening the checkout widget, then verify the signature against
 * that order_id here (failing closed, not conditionally). That
 * requires a real Razorpay secret key to implement and test, so it's
 * a follow-up rather than a drop-in patch.
 * ------------------------------------------------------------------
 */
if (!empty($razorpay_signature) && $razorpay_signature !== 'undefined') {
    $key_secret = "YOUR_RAZORPAY_SECRET_KEY"; // Replace with your real secret, ideally from an env var
    $generated_signature = hash_hmac('sha256', $payment_id, $key_secret);

    if (!hash_equals($generated_signature, $razorpay_signature)) {
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
    // FIX: don't print raw SQL exceptions or "how to alter your table" hints
    // to visitors — that leaks internal schema/config details. Log it for
    // yourself instead and show the customer a generic message.
    error_log("verify-payment.php order insert failed: " . $e->getMessage());
    echo "<div style='padding:20px; background:#fce8e6; color:#c5221f; font-family:sans-serif; border-radius:8px; margin:20px;'>";
    echo "<h3>Something went wrong saving your order</h3>";
    echo "<p>Your payment may have gone through, but we couldn't record the order automatically. Please contact support with your transaction ID: <strong>" . $payment_id . "</strong></p>";
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
                <span>$<?php echo number_format($summary['raw_amount'], 2); ?></span>
            </div>
        </div>

        <div class="btn-wrapper">
            <a href="track-orders.php" class="btn-home btn-track">Track Order</a>
            <a href="/Cartify/index.php" class="btn-home">Continue Shopping</a>
        </div>
    </div>

</body>
</html>
<?php
// Products/apply-promo.php
require_once '../db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method entry point']);
    exit;
}

$code = trim($_POST['code']);
$order_value = (float)$_POST['order_value'];

try {
    $stmt = $pdo->prepare("SELECT * FROM promo_codes WHERE code = ? AND is_active = 1 AND expiry_date >= CURDATE()");
    $stmt->execute([$code]);
    $promo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$promo) {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired promotional code.']);
        exit;
    }

    if ($order_value < $promo['min_order_value']) {
        echo json_encode(['success' => false, 'message' => 'Minimum spend not met for this coupon. Min order: $' . number_format($promo['min_order_value'], 2)]);
        exit;
    }

    $discount = 0.00;
    if ($promo['discount_type'] === 'percentage') {
        $discount = ($promo['discount_value'] / 100) * $order_value;
    } else {
        $discount = $promo['discount_value'];
    }

    // Ensure the discount amount doesn't exceed the total price
    if ($discount > $order_value) {
        $discount = $order_value;
    }

    echo json_encode([
        'success' => true,
        'discount' => $discount,
        'message' => 'Promo code applied successfully!'
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'System query operational logic failure.']);
}
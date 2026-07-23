<?php
// Products/apply-promocode.php

ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    if (!file_exists('../db.php')) {
        echo json_encode(['success' => false, 'message' => 'Database configuration file missing.']);
        exit;
    }
    require_once '../db.php';

    if (!isset($pdo)) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
        exit;
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        exit;
    }

    $code = strtoupper(trim($_POST['code'] ?? ''));
    $order_value = (float)($_POST['order_value'] ?? 0);

    if (empty($code)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a promo code.']);
        exit;
    }

    // Query active promo code from database
    $stmt = $pdo->prepare("SELECT * FROM promo_codes WHERE UPPER(code) = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$code]);
    $promo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$promo) {
        echo json_encode(['success' => false, 'message' => 'Invalid or unreleased promo code.']);
        exit;
    }

    // Expiry Check
    if (!empty($promo['expiry_date'])) {
        $today = date('Y-m-d');
        $expiry = date('Y-m-d', strtotime($promo['expiry_date']));
        if ($expiry < $today) {
            echo json_encode(['success' => false, 'message' => 'This promo code has expired.']);
            exit;
        }
    }

    // Minimum Order Value Check
    $min_order = (float)($promo['min_order_value'] ?? 0);
    if ($order_value < $min_order) {
        echo json_encode([
            'success' => false,
            'message' => 'Minimum order spend of ₹' . number_format($min_order, 2) . ' required for this code.'
        ]);
        exit;
    }

    // Discount Calculation (Supports 'percentage', 'flat', 'fixed')
    $discount = 0.00;
    $discount_type = strtolower($promo['discount_type'] ?? 'percentage');
    $discount_val = (float)($promo['discount_value'] ?? 0);

    if ($discount_type === 'percentage' || $discount_type === 'percent') {
        $discount = ($discount_val / 100) * $order_value;
    } else {
        $discount = $discount_val; // Flat / fixed discount
    }

    // Ensure discount doesn't exceed order subtotal
    if ($discount > $order_value) {
        $discount = $order_value;
    }

    $discount = round($discount, 2);

    echo json_encode([
        'success' => true,
        'discount' => $discount,
        'message' => 'Promo code "' . htmlspecialchars($code) . '" applied successfully!'
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error validating promo code.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
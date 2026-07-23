<?php
// Product/add-to-cart.php
require_once '../db.php';
session_start();

// 1. Check if a valid product ID was provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product_id = (int)$_GET['id'];
$quantity = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;

try {
    // 2. Verify the product exists and is actually in stock
    $stmt = $pdo->prepare("SELECT id, title, price, in_stock FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product || $product['in_stock'] == 0) {
        // Product doesn't exist or is out of stock, send them back
        header("Location: product-detail.php?id=" . $product_id . "&error=outofstock");
        exit;
    }

    // 3. Initialize the cart session array if it doesn't exist yet
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // 4. Add or increment the product in the session cart
    if (isset($_SESSION['cart'][$product_id])) {
        // Item already in cart? Just add to the quantity
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        // New item? Assign the initial quantity
        $_SESSION['cart'][$product_id] = $quantity;
    }

    // 5. Redirect back to the product details page with a success flag
    header("Location: product-detail.php?id=" . $product_id . "&success=added");
    exit;

} catch (PDOException $e) {
    die("Error processing cart action: " . $e->getMessage());
}
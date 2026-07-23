<?php
// Merchant/manage-product.php
require_once '../db.php';
session_start();

// Guard Protection
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'merchant') {
    header("Location: ../index.php?error=unauthorized");
    exit;
}

$merchant_id = $_SESSION['user_id'];

if (isset($_GET['action']) && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    $action = $_GET['action'];

    // Verify that the product actually belongs to this logged-in merchant
    $check = $pdo->prepare("SELECT image_url FROM products WHERE id = ? AND merchant_id = ?");
    $check->execute([$product_id, $merchant_id]);
    $product = $check->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("Unauthorized action or product not found.");
    }

    // Handle Action: DELETE
    if ($action === 'delete') {
        // Delete physical image file from uploads folder to save storage space
        if (!empty($product['image_url']) && file_exists('../' . $product['image_url'])) {
            unlink('../' . $product['image_url']);
        }
        
        $delete = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $delete->execute([$product_id]);
        header("Location: dashboard.php?status=deleted");
        exit;
    }

    // Handle Action: TOGGLE STOCK
    if ($action === 'toggle_stock') {
        $stmt = $pdo->prepare("UPDATE products SET in_stock = 1 - in_stock WHERE id = ?");
        $stmt->execute([$product_id]);
        header("Location: dashboard.php?status=stock_updated");
        exit;
    }
}

header("Location: dashboard.php");
exit;
?>
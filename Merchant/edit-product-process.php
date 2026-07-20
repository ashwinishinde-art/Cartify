<?php
// Merchant/edit-product-process.php
require_once '../db.php';
session_start();

// Guard Protection
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'merchant' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit;
}

$merchant_id = $_SESSION['user_id'];
$product_id = (int)$_POST['id'];
$title = trim($_POST['title']);
$price = floatval($_POST['price']);
$category = trim($_POST['category']);
$description = trim($_POST['description']);

if (empty($title) || $price <= 0 || empty($category) || empty($description)) {
    die("All fields are required and price must be greater than zero.");
}

try {
    // 1. Double-check ownership of the item
    $check = $pdo->prepare("SELECT image_url FROM products WHERE id = ? AND merchant_id = ?");
    $check->execute([$product_id, $merchant_id]);
    $current_product = $check->fetch(PDO::FETCH_ASSOC);

    if (!$current_product) {
        die("Unauthorized modification attempt.");
    }

    $image_url = $current_product['image_url']; // Default to current path

    // 2. Check if a new replacement image was uploaded
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $file = $_FILES['product_image'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];

        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = array('jpg', 'jpeg', 'png', 'webp');

        if (in_array($fileExt, $allowedExts)) {
            if ($fileSize < 5000000) { // 5MB limit
                $newFileName = uniqid('prod_', true) . "." . $fileExt;
                $uploadDirectory = '../uploads/';
                $fileDestination = $uploadDirectory . $newFileName;

                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    // Delete old physical image from storage to save space
                    if (!empty($current_product['image_url']) && file_exists('../' . $current_product['image_url'])) {
                        unlink('../' . $current_product['image_url']);
                    }
                    // Use new path
                    $image_url = 'uploads/' . $newFileName;
                } else {
                    die("Failed to save the new uploaded image.");
                }
            } else {
                die("File is too large! Maximum allowed is 5MB.");
            }
        } else {
            die("Invalid image format. Allowed formats: JPG, JPEG, PNG, WEBP.");
        }
    }

    // 3. Update the database record
    $stmt = $pdo->prepare("UPDATE products SET title = ?, description = ?, price = ?, category = ?, image_url = ? WHERE id = ? AND merchant_id = ?");
    if ($stmt->execute([$title, $description, $price, $category, $image_url, $product_id, $merchant_id])) {
        header("Location: dashboard.php?status=updated");
        exit;
    }

} catch (PDOException $e) {
    die("Database update error: " . $e->getMessage());
}
?>
<?php
// Merchant/add-product-process.php
require_once '../db.php';
session_start();

// Guard Protection: Only merchants can process uploads
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'merchant') {
    header("Location: ../index.php?error=unauthorized");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $merchant_id = (int)$_SESSION['user_id'];
    $title = trim($_POST['title'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // 1. Basic text fields validation
    if (empty($title) || $price <= 0 || empty($category) || empty($description)) {
        die("Please fill in all fields correctly.");
    }

    // 2. Multiple Images Upload Handling
    if (!isset($_FILES['product_images']) || empty($_FILES['product_images']['name'][0])) {
        die("Please select at least one product image to upload.");
    }

    $uploaded_images = [];
    $uploadDirectory = '../uploads/';

    // Ensure uploads directory exists on your server
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    // Loop through uploaded files (Max 5)
    $total_files = count($_FILES['product_images']['name']);
    $limit = min($total_files, 5); // Enforce a 5-image upper cap
    $allowedExts = array('jpg', 'jpeg', 'png', 'webp');

    for ($i = 0; $i < $limit; $i++) {
        $fileName     = $_FILES['product_images']['name'][$i];
        $fileTmpName  = $_FILES['product_images']['tmp_name'][$i];
        $fileSize     = $_FILES['product_images']['size'][$i];
        $fileError    = $_FILES['product_images']['error'][$i];

        // Get file extension safely
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileError === 0 && in_array($fileExt, $allowedExts)) {
            if ($fileSize < 5000000) { // 5MB Limit per image
                $newFileName = uniqid('prod_', true) . "." . $fileExt;
                $fileDestination = $uploadDirectory . $newFileName;

                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    $uploaded_images[] = 'uploads/' . $newFileName;
                }
            } else {
                die("One of your images is too large! Maximum allowed size per image is 5MB.");
            }
        }
    }

    if (empty($uploaded_images)) {
        die("Invalid image upload. Please select valid images (JPG, JPEG, PNG, or WEBP under 5MB).");
    }

    // 3. Database Transactions: Insert product and save multi-gallery paths
    try {
        $pdo->beginTransaction();

        // Use the first image as the primary cover photo for product listings
        $main_cover_image = $uploaded_images[0];

        // Insert primary product details into 'products' table
        $stmt = $pdo->prepare("INSERT INTO products (merchant_id, title, description, price, image_url, category) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$merchant_id, $title, $description, $price, $main_cover_image, $category]);
        
        $new_product_id = $pdo->lastInsertId();

        // Insert all images (including secondary ones) into the gallery table if present
        $imgCheck = $pdo->query("SHOW TABLES LIKE 'product_images'");
        if ($imgCheck->rowCount() > 0) {
            $imgStmt = $pdo->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
            foreach ($uploaded_images as $img_url) {
                $imgStmt->execute([$new_product_id, $img_url]);
            }
        }

        $pdo->commit();

        // Redirect back to merchant dashboard on success
        header("Location: dashboard.php?status=product_added");
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Database error adding product: " . $e->getMessage());
    }

} else {
    header("Location: add-product.php");
    exit;
}
?>
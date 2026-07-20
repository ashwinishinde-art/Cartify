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
    $merchant_id = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);

    // 1. Basic validation
    if (empty($title) || $price <= 0 || empty($category) || empty($description)) {
        die("Please fill in all fields correctly.");
    }

    // 2. Dynamic Image Upload Handling
    $image_url = "";
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $file = $_FILES['product_image'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];

        // Get file extension safely
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = array('jpg', 'jpeg', 'png', 'webp');

        if (in_array($fileExt, $allowedExts)) {
            // Check file size limits (under 5MB)
            if ($fileSize < 5000000) {
                // Generate a unique file name to avoid overwriting existing uploads
                $newFileName = uniqid('prod_', true) . "." . $fileExt;
                
                // Destination path to save physical image
                $uploadDirectory = '../uploads/';
                $fileDestination = $uploadDirectory . $newFileName;

                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    // This path will be saved to your DB to show images dynamically on your store
                    $image_url = 'uploads/' . $newFileName;
                } else {
                    die("Failed to save the uploaded image.");
                }
            } else {
                die("The image is too large! Max size allowed is 5MB.");
            }
        } else {
            die("Invalid file format. You can only upload JPG, JPEG, PNG, or WEBP.");
        }
    } else {
        die("Please select a product image to upload.");
    }

    // 3. Insert Product details into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO products (merchant_id, title, description, price, image_url, category) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$merchant_id, $title, $description, $price, $image_url, $category])) {
            // Redirect back to dashboard on success
            header("Location: dashboard.php?status=product_added");
            exit;
        }
    } catch (PDOException $e) {
        die("Database error adding product: " . $e->getMessage());
    }

} else {
    header("Location: add-product.php");
    exit;
}
?>
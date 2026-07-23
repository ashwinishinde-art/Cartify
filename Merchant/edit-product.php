<?php
// Merchant/edit-product.php
require_once '../db.php';
session_start();

// Guard Protection
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'merchant') {
    header("Location: ../index.php?error=unauthorized");
    exit;
}

$merchant_id = $_SESSION['user_id'];

// Get Product ID from URL
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$product_id = (int)$_GET['id'];

// Retrieve existing product details
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND merchant_id = ?");
    $stmt->execute([$product_id, $merchant_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("Product not found or unauthorized access.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Cartify</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../header.css">
    
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #fafdf8;
            margin: 0;
            color: #1a1a1a;
        }
        .form-container {
            max-width: 650px;
            margin: 50px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 28px;
            box-shadow: 0 10px 30px rgba(85, 98, 70, 0.05);
            border: 1px solid rgba(85, 98, 70, 0.08);
        }
        .form-container h1 {
            color: #5D6247;
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 22px;
        }
        .form-group label {
            font-weight: 600;
            color: #5D6247;
            font-size: 0.95rem;
        }
        .form-group input, .form-group textarea, .form-group select {
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            outline: none;
            background-color: #fafdf8;
            transition: border-color 0.2s;
            width: 100%;
            box-sizing: border-box;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            border-color: #5D6247;
            background-color: #ffffff;
        }
        .row-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .current-image-preview {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #f5f7f3;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 10px;
        }
        .current-image-preview img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        .btn-submit {
            width: 100%;
            background-color: #5D6247;
            color: #ffffff;
            border: none;
            padding: 16px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 14px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(85, 98, 70, 0.15);
            transition: background-color 0.2s, transform 0.2s;
        }
        .btn-submit:hover {
            background-color: #424d36;
            transform: translateY(-2px);
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #666;
            text-decoration: none;
            font-size: 0.95rem;
        }
        .back-link:hover {
            color: #5D6247;
        }
    </style>
</head>
<body>

<header class="header">
    <a href="../index.php" class="logo">Cartify</a>
    <nav class="nav-links">
        <a href="dashboard.php">Dashboard</a>
    </nav>
</header>

    <main class="form-container">
        <h1>Edit Product Details</h1>

        <form action="edit-product-process.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

            <div class="form-group">
                <label for="title">Product Title</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required>
            </div>

            <div class="row-group">
                <div class="form-group">
                    <label for="price">Price ($)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="Fashion" <?php echo ($product['category'] === 'Fashion') ? 'selected' : ''; ?>>Fashion</option>
                        <option value="Home" <?php echo ($product['category'] === 'Home') ? 'selected' : ''; ?>>Home</option>
                        <option value="Instruments" <?php echo ($product['category'] === 'Instruments') ? 'selected' : ''; ?>>Instruments</option>
                        <option value="Accessories" <?php echo ($product['category'] === 'Accessories') ? 'selected' : ''; ?>>Accessories</option>
                        <option value="Electronics" <?php echo ($product['category'] === 'Electronics') ? 'selected' : ''; ?>>Electronics</option>
                        <option value="Sports" <?php echo ($product['category'] === 'Sports') ? 'selected' : ''; ?>>Sports</option>
                        <option value="Groceries" <?php echo ($product['category'] === 'Groceries') ? 'selected' : ''; ?>>Groceries</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Product Description</label>
                <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Current Product Image</label>
                <div class="current-image-preview">
                    <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" alt="Current Image">
                    <span style="font-size: 0.9rem; color: #666;">This image is currently active. Upload a new one below to replace it.</span>
                </div>
            </div>

            <div class="form-group">
                <label for="product_image">Replace Image (Optional)</label>
                <input type="file" id="product_image" name="product_image" accept="image/*">
            </div>

            <button type="submit" class="btn-submit">Save Changes</button>
            <a href="dashboard.php" class="back-link">← Cancel Changes</a>
        </form>
    </main>

</body>
</html>
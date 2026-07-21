<?php
session_start();

// Guard Protection: Only merchants can add products
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'merchant') {
    header("Location: ../index.php?error=unauthorized");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Merchant Dashboard</title>
    <link rel="stylesheet" href="../header.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
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
        /* Custom file upload styling */
        .file-input-wrapper {
            position: relative;
            margin-bottom: 10px;
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
    <img src="../assets/logo.png" height=50px alt="Shop Logo" class="logo">
    <nav class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="../index.php">View Shop</a>
    </nav>
</header>

    <main class="form-container">
        <h1>Add New Product</h1>

        <form action="add-product-process.php" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="title">Product Title</label>
                <input type="text" id="title" name="title" placeholder="e.g. Classic Cotton T-Shirt" required>
            </div>

            <div class="row-group">
                <div class="form-group">
                    <label for="price">Price ($)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0.01" placeholder="29.99" required>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Fashion">Fashion</option>
                        <option value="Home">Home</option>
                        <option value="Instruments">Instruments</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Sports">Sports</option>
                        <option value="Groceries">Groceries</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Product Description</label>
                <textarea id="description" name="description" rows="5" placeholder="Describe the item's features, material, and sizing details..." required></textarea>
            </div>

            <div class="form-group">
                <label for="product_image">Product Image</label>
                <input type="file" id="product_image" name="product_image" accept="image/*" required>
            </div>

            <button type="submit" class="btn-submit">Publish Product</button>
            <a href="dashboard.php" class="back-link">← Cancel & Back to Dashboard</a>
        </form>
    </main>

</body>
</html>
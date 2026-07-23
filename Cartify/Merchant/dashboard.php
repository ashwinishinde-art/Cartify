<?php
// MUST BE LINE 1, WITH NO SPACES BEFORE <?php
require_once '../db.php';
session_start();

if (!isset($_SESSION['user_id']) && !isset($_SESSION['user']['id'])) {
    header("Location: /Cartify/login.php");
    exit;
}

$current_logged_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : (int)$_SESSION['user']['id'];

// 1. Check live role status instantly
$roleCheck = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$roleCheck->execute([$current_logged_id]);
$liveUser = $roleCheck->fetch(PDO::FETCH_ASSOC);

if (!$liveUser || $liveUser['role'] !== 'merchant') {
    if (isset($_SESSION['user']['role'])) {
        $_SESSION['user']['role'] = 'customer'; 
    }
    // Smooth redirect back to storefront home screen
    header("Location: /Cartify/index.php?error=revoked");
    exit;
}

// 2. FETCH INVENTORY: Populate the $products array using the current merchant's id column
$products = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE merchant_id = ? ORDER BY created_at DESC");
    $stmt->execute([$current_logged_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Fail safely if database encounters an execution slipup
    die("Database error loading your products: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merchant Dashboard - Cartify</title>
    
    <link rel="stylesheet" href="../header.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <img src=" ../assets/logo.png" height=65px alt="logo">
    <nav class="nav-links">
        <a href="../index.php">View Shop</a>
    </nav>
    <div class="nav-icons">
        <a href="../logout.php" style="color: #1a1a1a; text-decoration: none; font-weight: 500;">Logout</a>
    </div>
</header>

    <main class="dashboard-container">
        <div class="dashboard-header">
            <div>
                <h1><?php echo htmlspecialchars($_SESSION['store_name'] ?? 'Merchant Store'); ?></h1>
                <p style="color: #666; margin-top: 5px;">Manage your shop products and listing inventory</p>
            </div>
            <a href="add-product.php" class="btn-add">+ Add New Product</a>
        </div>

        <div class="inventory-card">
            <h2>Live Inventory</h2>
            
            <?php if (count($products) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $prod): ?>
                            <tr>
                                <td>
                                    <img src="../<?php echo htmlspecialchars($prod['image_url']); ?>" class="prod-img" alt="Product Image">
                                </td>
                                <td>
                                    <span class="prod-title"><?php echo htmlspecialchars($prod['title']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($prod['category']); ?></td>
                                <td>₹<?php echo number_format($prod['price'], 2); ?></td>
                                <td>
                                    <?php if ($prod['in_stock'] == 1): ?>
                                        <span class="badge badge-in-stock">In Stock</span>
                                    <?php else: ?>
                                        <span class="badge badge-out-stock">Out of Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions-group">
                                        <a href="edit-product.php?id=<?php echo $prod['id']; ?>" class="btn-action btn-stock" style="background-color: #e0f2fe; color: #0369a1;">
        Edit Details
    </a>
                                        <a href="manage-product.php?action=toggle_stock&id=<?php echo $prod['id']; ?>" class="btn-action btn-stock">
                                            <?php echo ($prod['in_stock'] == 1) ? 'Mark Sold Out' : 'Mark In Stock'; ?>
                                        </a>
                                        <a href="manage-product.php?action=delete&id=<?php echo $prod['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this product?');">
                                            Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No products uploaded yet</h3>
                    <p>Click "+ Add New Product" to publish your first inventory item to the shop!</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>
<?php
// Product/product-detail.php
require_once '../db.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product_id = (int)$_GET['id'];

try {
    // Fetch product details along with merchant store details
    $stmt = $pdo->prepare("
        SELECT p.*, u.store_name 
        FROM products p 
        JOIN users u ON p.merchant_id = u.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("Product not found.");
    }

    // Explode gallery image strings into arrays if they exist
    $gallery_images = [];
    if (!empty($product['image_gallery'])) {
        $gallery_images = explode(',', $product['image_gallery']);
    }
    // Always insert the main image as the prime index item
    array_unshift($gallery_images, $product['image_url']);
    // Add this inside the try{} block right beneath your product lookup in product-detail.php
$reviewStmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC");
$reviewStmt->execute([$product_id]);
$real_reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error loading details: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['title']); ?> - Cartify</title>
    
    <!-- Google Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Absolute Root Path Stylesheet Linking -->
    <link rel="stylesheet" href="../header.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="product-detail.css?v=<?php echo time(); ?>">
</head>
<body>

    <!-- INTEGRATED ROOT-SAFE HEADER -->
    <header class="header">
    <a href="../index.php" class="logo">Cartify</a>
    
    <nav class="nav-links">
        <div class="nav-menu-wrapper" style="display: flex; align-items: center; gap: 20px;">
            <div class="standard-links" id="standardLinks">
                <a href="../index.php">Home</a>
                <a href="#">About</a>
                <a href="#">Contact</a>
            </div>

            <form action="/Cartify/Products/products.php" method="GET" class="search-container" id="searchForm">
    <div class="search-input-wrapper" id="searchInputWrapper">
        <input type="text" name="search" class="search-input" id="searchInput" placeholder="Search brands, products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" required>
    </div>
</form>
        </div>
    </nav>

    <div class="nav-icons" style="display: flex; align-items: center; gap: 15px;">
        
        <button type="button" class="search-btn" id="searchTriggerBtn" aria-label="Toggle Search" style="background: none; border: none; padding: 0; color: inherit; cursor: pointer;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </button>

        <?php if (isset($_SESSION['user_name'])): ?>
            <div class="user-dropdown">
                <span class="dropdown-trigger">
                    Hello <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>! ▼
                </span>
                <div class="dropdown-content">
                    <a href="#">Track Order</a>
                    <a href="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'logout.php' : '../logout.php'; ?>" class="logout-link">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a href="Login" aria-label="Login">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </a>
        <?php endif; ?>

        <!-- Replace the Cart SVG structure in your header with this -->
<div style="position: relative; display: flex; align-items: center; cursor: pointer;">
<a href="cart.php" style="position: relative; display: flex; align-items: center; cursor: pointer; color: inherit; text-decoration: none;">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <path d="M16 10a4 4 0 0 1-8 0"></path>
    </svg>
    <!-- Keep your dynamic item counter badge code right here below the SVG -->
</a>
    
    <!-- Dynamic Cart Counter Badge -->
    <?php 
    $cart_count = 0;
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $cart_count = array_sum($_SESSION['cart']); // Sums up total quantity of all items
    }
    if ($cart_count > 0): 
    ?>
        <span style="position: absolute; top: -8px; right: -10px; background-color: #c62828; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.75rem; font-weight: 700; font-family: 'Inter', sans-serif;">
            <?php echo $cart_count; ?>
        </span>
    <?php endif; ?>
</div>
    </div>
</header>



    <main class="detail-container">
        <div class="product-layout">
            
            <!-- LEFT COLUMN: PHOTO GALLERY SYSTEM -->
            <div class="gallery-section">
                <div class="main-image-wrapper">
                    <!-- FIX: absolute root folder path used to render image -->
                    <img id="primeViewer" src="/Cartify/<?php echo htmlspecialchars($gallery_images[0]); ?>" alt="Product Prime View">
                </div>
                
                <?php if (count($gallery_images) > 1): ?>
                    <div class="thumbnail-list">
                        <?php foreach ($gallery_images as $index => $img_path): ?>
                            <img class="thumb-img <?php echo $index === 0 ? 'active' : ''; ?>" 
                                 src="/Cartify/<?php echo htmlspecialchars($img_path); ?>" 
                                 alt="Thumb" 
                                 onclick="switchImage(this)">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- RIGHT COLUMN: ITEM SPECS -->
            <div class="info-section">
                <span class="merchant-tag">Shop: <?php echo htmlspecialchars($product['store_name']); ?></span>
                <h1><?php echo htmlspecialchars($product['title']); ?></h1>
                
                <div class="price-tag">$<?php echo number_format($product['price'], 2); ?></div>

                <div class="description-box">
                    <h3>Product Overview</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

<div class="action-buttons-group">
    <?php if ($product['in_stock'] == 1): ?>
        <!-- Add to Cart (Secondary style) -->
       <!-- Update this inside the action-buttons-group in Product/product-detail.php -->
<a href="add-to-cart.php?id=<?php echo $product['id']; ?>" class="btn-action btn-cart">Add to Cart</a>
        
        <!-- Buy Now (Primary call-to-action style) -->
        <a href="checkout.php?product_id=<?php echo $product['id']; ?>&qty=1" class="btn-action btn-buy-now">Buy Now</a>
    <?php else: ?>
        <!-- Out of stock fallback spanning across full layout width -->
        <button class="btn-action btn-disabled" disabled>Out of Stock</button>
    <?php endif; ?>
</div>
            </div>
        </div>

      <!-- LOWER SECTION: DYNAMIC CUSTOMER REVIEWS -->
<div class="reviews-section">
    <h2>Customer Ratings & Reviews</h2>
    
    <?php if (empty($real_reviews)): ?>
        <p style="color: #666; font-style: italic;">No reviews posted yet for this item. Be the first to buy and share your thoughts!</p>
    <?php else: ?>
        <?php foreach ($real_reviews as $rev): ?>
            <div class="review-card">
                <div class="review-header">
                    <span><?php echo htmlspecialchars($rev['user_name']); ?> (Verified Purchase)</span>
                    <span class="review-stars">
                        <?php echo str_repeat('★', $rev['rating']) . str_repeat('☆', 5 - $rev['rating']); ?>
                    </span>
                </div>
                <p style="margin: 0; color:#555;"><?php echo nl2br(htmlspecialchars($rev['comment'])); ?></p>
                <span style="font-size: 0.75rem; color:#aaa; display:block; margin-top:8px;">Posted on: <?php echo date('M d, Y', strtotime($rev['created_at'])); ?></span>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
    </main>

    <script>
    function switchImage(thumbnail) {
        document.getElementById('primeViewer').src = thumbnail.src;
        document.querySelectorAll('.thumb-img').forEach(img => img.classList.remove('active'));
        thumbnail.classList.add('active');
    }

    document.getElementById('searchTriggerBtn').addEventListener('click', function() {
        const wrapper = document.getElementById('searchInputWrapper');
        wrapper.classList.toggle('active');
    });
    </script>
</body>
</html>
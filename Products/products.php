<?php
// Product/products.php
require_once '../db.php';
session_start();

$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$products = [];

try {
    if ($search_query !== '') {
        // Fetch matching items only if they belong to an authorized merchant
        $stmt = $pdo->prepare("
            SELECT p.* 
            FROM products p 
            JOIN users u ON p.merchant_id = u.id 
            WHERE u.role = 'merchant' 
              AND (p.title LIKE ? OR p.description LIKE ? OR p.category LIKE ?)
            ORDER BY p.created_at DESC
        ");
        
        $wildcard = "%" . $search_query . "%";
        $stmt->execute([$wildcard, $wildcard, $wildcard]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // DEFAULT STATE: Fetch all catalog products belonging to active merchants
        $stmt = $pdo->query("
            SELECT p.* 
            FROM products p 
            JOIN users u ON p.merchant_id = u.id 
            WHERE u.role = 'merchant'
            ORDER BY p.created_at DESC
        ");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Database Error loading catalog products: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Cartify</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Using absolute root directory structure targets your layout files perfectly -->
    <link rel="stylesheet" href="header.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="products.css?v=<?php echo time(); ?>">
</head>
<body>

    <header class="header">
        <a href="../index.php" class="logo"><img src="../assets/logo.png" height="60px"></a>
    
    <nav class="nav-links">
        <div class="nav-menu-wrapper" style="display: flex; align-items: center; gap: 20px;">
            <div class="standard-links" id="standardLinks">
                <a href="../index.php">Home</a>
                <a href="About/about.html">About</a>
                <a href="Contact/contact.html">Contact</a>
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


    <main class="shop-container">
        
        <?php if ($search_query === ''): ?>
            <div class="empty-shop">
                <div class="large-icon">🔍</div>
                <h2>Search to Begin Shopping</h2>
                <p>Click on the magnifying glass icon above and search for clothes, accessories, home items, and more!</p>
            </div>

        <?php else: ?>
            <div class="shop-header">
                <h1>Search Results</h1>
                <p>Found <?php echo count($products); ?> matches for "<strong><?php echo htmlspecialchars($search_query); ?></strong>"</p>
            </div>

            <?php if (count($products) > 0): ?>
                <div class="product-grid">
                    <?php foreach ($products as $prod): ?>
                        <div class="product-card">
                            
                            <!-- Image container with correct absolute root reference to image paths -->
                            <div class="product-image-box">
                                <a href="product-detail.php?id=<?php echo $prod['id']; ?>">
                                    <span class="category-badge"><?php echo htmlspecialchars($prod['category']); ?></span>
                                    <?php if ($prod['in_stock'] == 0): ?>
                                        <div class="sold-out-overlay"><span class="sold-out-badge">Sold Out</span></div>
                                    <?php endif; ?>
                                    <img src="/Cartify/<?php echo htmlspecialchars($prod['image_url']); ?>" alt="Product">
                                </a>
                            </div>

                            <!-- Consolidated single clean product-info layout block -->
                            <div class="product-info">
                                <span class="product-merchant"><?php echo htmlspecialchars($prod['store_name'] ?? 'Cartify Vendor'); ?></span>
                                <h3 class="product-title">
                                    <a href="product-detail.php?id=<?php echo $prod['id']; ?>">
                                        <?php echo htmlspecialchars($prod['title']); ?>
                                    </a>
                                </h3>
                                <p class="product-description"><?php echo htmlspecialchars($prod['description']); ?></p>
                                
                                <div class="product-footer">
                                    <span class="product-price">$<?php echo number_format($prod['price'], 2); ?></span>
                                    
                                    <!-- Update this inside the product-card footer loop in Product/products.php -->
<?php if ($prod['in_stock'] == 1): ?>
    <a href="add-to-cart.php?id=<?php echo $prod['id']; ?>" class="btn-buy">Add to Cart</a>
<?php else: ?>
    <button class="btn-buy disabled" disabled>Out of Stock</button>
<?php endif; ?>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-shop">
                    <div class="large-icon">😔</div>
                    <h2>No matching products found</h2>
                    <p>We couldn't find matches for "<?php echo htmlspecialchars($search_query); ?>". Check spelling or search for categories like 'Fashion' or 'Home'.</p>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </main>

    <script>
    document.getElementById('searchTriggerBtn').addEventListener('click', function(e) {
        const wrapper = document.getElementById('searchInputWrapper');
        const input = document.getElementById('searchInput');
        const form = document.getElementById('searchForm');
        const links = document.getElementById('standardLinks');

        if (!wrapper.classList.contains('active')) {
            wrapper.classList.add('active');
            links.style.opacity = '0.3';
            input.focus();
        } else {
            if (input.value.trim() !== "") {
                form.submit();
            } else {
                wrapper.classList.remove('active');
                links.style.opacity = '1';
            }
        }
    });

    document.addEventListener('click', function(e) {
        const container = document.querySelector('.nav-menu-wrapper');
        const trigger = document.getElementById('searchTriggerBtn');
        const wrapper = document.getElementById('searchInputWrapper');
        const links = document.getElementById('standardLinks');
        
        if (!container.contains(e.target) && !trigger.contains(e.target)) {
            wrapper.classList.remove('active');
            links.style.opacity = '1';
        }
    });
    </script>
</body>
</html>
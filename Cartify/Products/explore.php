<?php
// Products/explore.php
require_once '../db.php';
session_start();

// Fetch active categories for filter tags
try {
    $categoryStmt = $pdo->query("SELECT DISTINCT category FROM products WHERE in_stock = 1 AND category IS NOT NULL AND category != ''");
    $categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $categories = [];
}

// Get filter inputs
$selected_category = $_GET['category'] ?? 'all';
$search_query = trim($_GET['search'] ?? '');

// Build dynamic query
$sql = "SELECT * FROM products WHERE in_stock = 1";
$params = [];

if ($selected_category !== 'all' && !empty($selected_category)) {
    $sql .= " AND category = ?";
    $params[] = $selected_category;
}

if (!empty($search_query)) {
    $sql .= " AND (title LIKE ? OR description LIKE ? OR category LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

$sql .= " ORDER BY created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Marketplace - Cartify</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="header.css?v=<?php echo time(); ?>">
    
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: #fafdf8; color: #1a1a1a; }

        .explore-container { max-width: 1280px; margin: 40px auto; padding: 0 24px; }
        
        .page-header { text-align: center; margin-bottom: 32px; }
        .page-header h1 { font-size: 2.2rem; color: #5D6247; font-weight: 700; margin-bottom: 8px; }
        .page-header p { color: #666; font-size: 1rem; }

        /* Filter Chips Bar */
        .filter-bar { display: flex; align-items: center; justify-content: center; gap: 12px; flex-wrap: wrap; margin-bottom: 40px; }
        .filter-chip { 
            display: inline-block; padding: 10px 20px; border-radius: 50px; background: #ffffff; 
            border: 1.5px solid #e2e8f0; color: #1a1a1a; text-decoration: none; font-size: 0.9rem; 
            font-weight: 500; transition: all 0.2s ease; 
        }
        .filter-chip:hover, .filter-chip.active { background: #5D6247; color: #ffffff; border-color: #5D6247; }

        /* Product Grid Matrix */
        .products-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); 
            gap: 28px; 
        }

        .product-card { 
            background: #ffffff; border-radius: 16px; padding: 16px; border: 1px solid rgba(85, 98, 70, 0.1); 
            display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s ease, box-shadow 0.2s ease; 
        }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,0.06); }

        .img-wrapper { width: 100%; height: 220px; border-radius: 12px; overflow: hidden; background: #f4f5f0; margin-bottom: 14px; }
        .img-wrapper img { width: 100%; height: 100%; object-fit: cover; }

        .category-tag { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #5D6247; letter-spacing: 0.5px; margin-bottom: 6px; display: block; }

        .product-title-link { color: #1a1a1a; text-decoration: none; font-weight: 600; font-size: 1.05rem; line-height: 1.3; display: block; margin-bottom: 8px; }
        .product-title-link:hover { color: #5D6247; }

        .price-row { display: flex; align-items: center; justify-content: space-between; margin-top: 12px; padding-top: 12px; border-top: 1px solid #f0f0f0; }
        .price-text { font-size: 1.2rem; font-weight: 700; color: #1a1a1a; }

        .btn-add-cart { 
            padding: 10px 16px; background: #5D6247; color: white; border: none; border-radius: 8px; 
            font-weight: 600; font-size: 0.88rem; cursor: pointer; transition: background 0.2s ease; 
        }
        .btn-add-cart:hover { background: #455038; }

        .empty-state { text-align: center; padding: 60px 20px; grid-column: 1 / -1; }
        .empty-state h3 { font-size: 1.4rem; color: #333; margin-bottom: 8px; }
    </style>
</head>
<body>

    <!-- Header Navigation Bar -->
   <header class="header">
    <a href="../index.php" class="logo"><img src="../assets/logo.png" height="60px"></a>
    
    <nav class="nav-links">
        <div class="nav-menu-wrapper" style="display: flex; align-items: center; gap: 20px;">
            <div class="standard-links" id="standardLinks">
                <a href="../index.php">Home</a>
                <a href="../Pages/About">About</a>
                <a href="../Pages/Contact">Contact</a>
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
                    <a href="track-orders.php">Track Order</a>
                    <a href="../logout.php" class="logout-link">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a href="../Login" aria-label="Login">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </a>
        <?php endif; ?>

        <div style="position: relative; display: flex; align-items: center; cursor: pointer;">
            <a href="cart.php" style="position: relative; display: flex; align-items: center; cursor: pointer; color: inherit; text-decoration: none;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
            </a>
            
            <?php 
            $cart_count = 0;
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                $cart_count = array_sum($_SESSION['cart']);
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

    <main class="explore-container">
        <div class="page-header">
            <h1>Explore Our Collection</h1>
            <p>Discover thousands of quality marketplace items curated just for you</p>
        </div>

        <!-- Category Filter Bar -->
        <div class="filter-bar">
            <a href="explore.php" class="filter-chip <?php echo ($selected_category === 'all') ? 'active' : ''; ?>">All Products</a>
            <?php foreach ($categories as $cat): ?>
                <a href="explore.php?category=<?php echo urlencode($cat); ?>" class="filter-chip <?php echo ($selected_category === $cat) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars(ucfirst($cat)); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Products Grid Matrix -->
        <div class="products-grid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div>
                            <div class="img-wrapper">
                                <a href="product-detail.php?id=<?php echo $product['id']; ?>">
                                    <img src="/Cartify/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                                </a>
                            </div>
                            <span class="category-tag"><?php echo htmlspecialchars($product['category'] ?? 'General'); ?></span>
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-title-link">
                                <?php echo htmlspecialchars($product['title']); ?>
                            </a>
                        </div>
                        
                        <div class="price-row">
                            <span class="price-text">₹<?php echo number_format($product['price'], 2); ?></span>
                            <a href="checkout.php?product_id=<?php echo $product['id']; ?>&qty=1" class="btn-add-cart" style="text-decoration: none;">Buy Now</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No products found</h3>
                    <p style="color:#666; margin-top:6px;">Try adjusting your search terms or category filters.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
    document.getElementById('searchTriggerBtn').addEventListener('click', function() {
        document.getElementById('searchInputWrapper').classList.toggle('active');
    });
    </script>
</body>
</html>
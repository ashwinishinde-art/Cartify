<?php
// Product/cart.php
require_once '../db.php';
session_start();

// Handle Quantity Updates or Item Removals
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $prod_id = (int)$_POST['product_id'];
        
        if ($_POST['action'] === 'update') {
            $new_qty = (int)$_POST['quantity'];
            if ($new_qty > 0) {
                $_SESSION['cart'][$prod_id] = $new_qty;
            }
        } elseif ($_POST['action'] === 'delete') {
            unset($_SESSION['cart'][$prod_id]);
        }
        
        header("Location: cart.php");
        exit;
    }
}

$cart_products = [];
$subtotal = 0.00;

// Fetch details for items currently sitting in the session cart
if (!empty($_SESSION['cart'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    try {
        $stmt = $pdo->prepare("SELECT id, title, price, image_url, in_stock FROM products WHERE id IN ($placeholders)");
        $stmt->execute(array_keys($_SESSION['cart']));
        $cart_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error loading cart inventory: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart - Cartify</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../header.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="cart.css?v=<?php echo time(); ?>">
</head>
<body>

    <!-- SYSTEM NAVBAR -->
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
<a href="Products/cart.php" style="position: relative; display: flex; align-items: center; cursor: pointer; color: inherit; text-decoration: none;">
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


    <!-- MAIN CART CONTAINER -->
    <main class="cart-container">
        <h1>Shopping Cart</h1>

        <?php if (empty($cart_products)): ?>
            <div class="empty-cart-state">
                <div style="font-size: 4rem; margin-bottom: 16px;">🛒</div>
                <h2>Your Cart is empty</h2>
                <p>Looks like you haven't added anything to your cart yet.</p>
                <a href="products.php?search=shirt" class="btn-shop-now">Explore Products</a>
            </div>
        <?php else: ?>
            <div class="cart-layout">
                <!-- LEFT SIDE: CART ITEMS -->
                <div class="cart-items-panel">
                    <?php foreach ($cart_products as $product): 
                        $qty = $_SESSION['cart'][$product['id']];
                        $item_total = $product['price'] * $qty;
                        $subtotal += $item_total;
                    ?>
                        <div class="cart-item-card">
                            <div class="item-img-box">
                                <img src="/Cartify/<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product Item">
                            </div>
                            
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                                <p class="stock-status <?php echo $product['in_stock'] ? 'in' : 'out'; ?>">
                                    <?php echo $product['in_stock'] ? 'In Stock' : 'Temporarily Out of Stock'; ?>
                                </p>
                                
                                <div class="item-actions">
                                    <!-- Update Quantity Form -->
                                    <form action="cart.php" method="POST" style="display: inline-flex; align-items: center; gap: 8px;">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="action" value="update">
                                        <label for="qty-<?php echo $product['id']; ?>" style="font-size: 0.85rem; color: #666;">Qty:</label>
                                        <select name="quantity" id="qty-<?php echo $product['id']; ?>" onchange="this.form.submit()" class="qty-select">
                                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                                <option value="<?php echo $i; ?>" <?php echo $i === $qty ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </form>

                                    <!-- Delete Item Form -->
                                    <form action="cart.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="delete-btn">Remove</button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="item-price-display">
                                $<?php echo number_format($item_total, 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- RIGHT SIDE: ORDER SUMMARY ORDER BLOCK -->
                <div class="cart-summary-panel">
                    <h2>Order Summary</h2>
                    <div class="summary-row">
                        <span>Items (<?php echo array_sum($_SESSION['cart']); ?>):</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span style="color: #137333; font-weight: 600;">FREE</span>
                    </div>
                    <hr>
                    <div class="summary-row total-row">
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script>
    document.getElementById('searchTriggerBtn').addEventListener('click', function() {
        document.getElementById('searchInputWrapper').classList.toggle('active');
    });
    </script>
</body>
</html>
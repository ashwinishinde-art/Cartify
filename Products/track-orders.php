<?php
// Products/track-orders.php
require_once '../db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_url = '/Cartify/';

$user_id = 1;
if (isset($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
} elseif (isset($_SESSION['user']['id'])) {
    $user_id = (int)$_SESSION['user']['id'];
}

// Handle Forms
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = (int)$_POST['order_id'];

    if (isset($_POST['action'])) {
        $new_status = ($_POST['action'] === 'cancel') ? 'Cancelled' : 'Returned';
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$new_status, $order_id, $user_id]);
    } 
    
    elseif (isset($_POST['submit_review'])) {
        $product_id = (int)$_POST['product_id'];
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);
        $user_name = $_SESSION['user_name'] ?? 'Anonymous Buyer';

        $stmt = $pdo->prepare("INSERT INTO reviews (product_id, user_id, user_name, rating, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$product_id, $user_id, $user_name, $rating, $comment]);

        $updateOrder = $pdo->prepare("UPDATE orders SET is_reviewed = 1 WHERE id = ?");
        $updateOrder->execute([$order_id]);
    }

    header("Location: track-orders.php");
    exit;
}

$stmt = $pdo->prepare("SELECT o.*, p.title FROM orders o JOIN products p ON o.product_id = p.id WHERE o.user_id = ? ORDER BY o.id DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History - Cartify</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="header.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="track-orders.css?v=<?php echo time(); ?>">
</head>
<body>

<header class="header">
    <a href="<?php echo $base_url; ?>index.php" class="logo">
        <img src="<?php echo $base_url; ?>assets/logo.png" height="60px" alt="Cartify Logo">
    </a>
    
    <nav class="nav-links">
        <div class="nav-menu-wrapper" style="display: flex; align-items: center; gap: 20px;">
            <div class="standard-links" id="standardLinks">
                <a href="<?php echo $base_url; ?>index.php">Home</a>
                <a href="<?php echo $base_url; ?>Pages/About">About</a>
                <a href="<?php echo $base_url; ?>Pages/Contact">Contact</a>
            </div>

            <form action="<?php echo $base_url; ?>Products/products.php" method="GET" class="search-container" id="searchForm">
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
                    Hello <?php echo htmlspecialchars(explode(' ', trim($_SESSION['user_name']))[0]); ?>! ▼
                </span>
                <div class="dropdown-content">
                    <a href="<?php echo $base_url; ?>Products/track-orders.php">Track Order</a>
                    <a href="<?php echo $base_url; ?>logout.php" class="logout-link">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a href="<?php echo $base_url; ?>Login" aria-label="Login">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </a>
        <?php endif; ?>

        <div style="position: relative; display: flex; align-items: center; cursor: pointer;">
            <a href="<?php echo $base_url; ?>Products/cart.php" style="position: relative; display: flex; align-items: center; cursor: pointer; color: inherit; text-decoration: none;">
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

    <main class="history-container">
        <h1>Your Purchase History</h1>

        <div class="table-card">
            <div class="table-header">
                <div class="col-id">Order ID</div>
                <div class="col-details">Product & Details</div>
                <div class="col-price">Total Price</div>
                <div class="col-destination">Delivery Destination</div>
                <div class="col-status">Fulfillment Status</div>
            </div>

            <?php if (empty($orders)): ?>
                <div style="padding: 40px; text-align: center; color: #666;">No orders found in your history.</div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="table-row-group">
                        <div class="table-row">
                            <div class="col-id order-id-num">#CF-<?php echo $order['id']; ?></div>
                            
                            <div class="col-details">
                                <span class="item-title"><?php echo htmlspecialchars($order['title']); ?></span>
                                <span class="item-qty">Qty: <?php echo $order['quantity']; ?></span>
                            </div>
                            
                            <div class="col-price order-price-bold">
                                ₹<?php echo number_format($order['total_price'], 2); ?>
                            </div>
                            
                            <div class="col-destination destination-info">
                                <strong class="dest-name"><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                                <div class="dest-address"><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></div>
                                <div class="phone-link">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="#5D6247" stroke="#5D6247">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                    <span><?php echo htmlspecialchars($order['customer_phone']); ?></span>
                                </div>
                            </div>
                            
                            <div class="col-status actions-box">
                                <?php 
                                    $status_lower = strtolower($order['status']);
                                    $display_txt = ($order['status'] === 'Pending') ? 'Pending' : $order['status'];
                                ?>
                                <span class="status-badge badge-<?php echo $status_lower; ?>"><?php echo $display_txt; ?></span>
                                
                                <?php if ($order['status'] === 'Pending'): ?>
                                    <form action="track-orders.php" method="POST" style="margin-top: 10px;">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <input type="hidden" name="action" value="cancel">
                                        <button type="submit" class="ctrl-btn cancel-action">✕ Cancel Order</button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($order['status'] === 'Delivered'): ?>
                                    <?php if ($order['is_reviewed'] == 0): ?>
                                        <button class="ctrl-btn review-action" style="margin-top: 10px;" onclick="toggleReviewForm(<?php echo $order['id']; ?>)">✨ Write Review</button>
                                    <?php else: ?>
                                        <span class="review-status-confirmed">✨ Review Submitted</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div id="review-panel-<?php echo $order['id']; ?>" class="review-drawer" style="display:none; padding: 20px 24px; background: #fafbfa; border-top: 1px dashed #edf0ed;">
                            <h4 style="margin:0 0 10px 0; color:#5D6247;">Submit Product Feedback</h4>
                            <form action="track-orders.php" method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <input type="hidden" name="product_id" value="<?php echo $order['product_id']; ?>">
                                <input type="hidden" name="submit_review" value="1">
                                <select name="rating" class="review-input" style="width:120px; display:inline-block; margin-right:10px; padding:6px; border-radius:6px; border:1px solid #ccd1cc;">
                                    <option value="5">★★★★★ (5)</option>
                                    <option value="4">★★★★☆ (4)</option>
                                    <option value="3">★★★☆☆ (3)</option>
                                    <option value="2">★★☆☆☆ (2)</option>
                                    <option value="1">★☆☆☆☆ (1)</option>
                                </select>
                                <input type="text" name="comment" class="review-input" style="width: calc(100% - 280px); display:inline-block; padding:6px; border-radius:6px; border:1px solid #ccd1cc;" placeholder="How is the quality of product?" required>
                                <button type="submit" style="padding: 7px 18px; background: #5D6247; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; margin-left: 8px;">Submit</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <script>
        function toggleReviewForm(id) {
            var el = document.getElementById('review-panel-' + id);
            el.style.display = (el.style.display === 'block') ? 'none' : 'block';
        }

        // Search Trigger Toggle JS
        document.getElementById('searchTriggerBtn').addEventListener('click', function(e) {
            const wrapper = document.getElementById('searchInputWrapper');
            const input = document.getElementById('searchInput');
            const form = document.getElementById('searchForm');
            const links = document.getElementById('standardLinks');

            if (!wrapper.classList.contains('active')) {
                wrapper.classList.add('active');
                if (links) links.style.opacity = '0.3';
                input.focus();
            } else {
                if (input.value.trim() !== "") {
                    form.submit();
                } else {
                    wrapper.classList.remove('active');
                    if (links) links.style.opacity = '1';
                }
            }
        });

        // Close search when clicking outside
        document.addEventListener('click', function(e) {
            const container = document.querySelector('.nav-menu-wrapper');
            const trigger = document.getElementById('searchTriggerBtn');
            const wrapper = document.getElementById('searchInputWrapper');
            const links = document.getElementById('standardLinks');
            
            if (container && wrapper && !container.contains(e.target) && !trigger.contains(e.target)) {
                wrapper.classList.remove('active');
                if (links) links.style.opacity = '1';
            }
        });
    </script>
</body>
</html>
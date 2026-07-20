<?php
// Products/track-orders.php
require_once '../db.php';
session_start();

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
    <link rel="stylesheet" href="/Cartify/header.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="track-orders.css?v=<?php echo time(); ?>">
</head>
<body>

    <header class="header">
        <a href="/Cartify/index.php" class="logo">Cartify</a>
    </header>

    <main class="history-container">
        <h1>Your Purchase History</h1>

        <div class="table-card">
            <!-- Restored original CSS header columns structure -->
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
                            <!-- Order ID column block -->
                            <div class="col-id order-id-num">#CF-<?php echo $order['id']; ?></div>
                            
                            <!-- Product column block -->
                            <div class="col-details">
                                <span class="item-title"><?php echo htmlspecialchars($order['title']); ?></span>
                                <span class="item-qty">Qty: <?php echo $order['quantity']; ?></span>
                            </div>
                            
                            <!-- Price column block -->
                            <div class="col-price order-price-bold">
                                ₹<?php echo number_format($order['total_price'] * 83, 0, '', ','); ?>
                            </div>
                            
                            <!-- Destination column block corrected back to dynamic div container structure -->
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
                            
                            <!-- Fulfillment column block mapped back to class alignment system -->
                            <div class="col-status actions-box">
                                <?php 
                                    $status_lower = strtolower($order['status']);
                                    // Fallback for visual text values matching original styles
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

                        <!-- Dropdown review form drawer block -->
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
    </script>
</body>
</html>
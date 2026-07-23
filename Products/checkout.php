<?php
// Products/checkout.php
require_once '../db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Global base URL definition for Cartify root
$base_url = '/Cartify/';

// Enforce login before payment
if (!isset($_SESSION['user_id']) && !isset($_SESSION['user']['id'])) {
    header("Location: " . $base_url . "Login/index.php?error=login_required_for_checkout");
    exit;
}

// Check user login status for header logic
$is_logged_in = isset($_SESSION['user_name']) && !empty($_SESSION['user_name']);

// Ensure the cart isn't empty before proceeding to payment
if (empty($_SESSION['cart']) && !isset($_GET['product_id'])) {
    header("Location: cart.php");
    exit;
}

// 1. Identify context: Direct item buy OR full session shopping cart checkout
$checkout_items = [];
$total_amount = 0.00;

if (isset($_GET['product_id'])) {
    // Single product flow ("Buy Now")
    $product_id = (int)$_GET['product_id'];
    $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
    
    $stmt = $pdo->prepare("SELECT id, title, price, image_url FROM products WHERE id = ? AND in_stock = 1");
    $stmt->execute([$product_id]);
    $prod = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($prod) {
        $prod['quantity'] = $qty;
        $prod['item_total'] = $prod['price'] * $qty;
        $checkout_items[] = $prod;
        $total_amount = $prod['item_total'];
    }
} elseif (!empty($_SESSION['cart'])) {
    // Session basket checkout flow
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $stmt = $pdo->prepare("SELECT id, title, price, image_url FROM products WHERE id IN ($placeholders) AND in_stock = 1");
    $stmt->execute(array_keys($_SESSION['cart']));
    $db_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($db_products as $prod) {
        $qty = $_SESSION['cart'][$prod['id']];
        $prod['quantity'] = $qty;
        $prod['item_total'] = $prod['price'] * $qty;
        $checkout_items[] = $prod;
        $total_amount += $prod['item_total'];
    }
}

// Redirect back if nothing is available to process
if (empty($checkout_items)) {
    header("Location: products.php");
    exit;
}

// Store basic summary variables in session for verification mapping later
$_SESSION['checkout_summary'] = [
    'items' => $checkout_items,
    'raw_amount' => $total_amount
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout - Cartify</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>header.css?v=<?php echo time(); ?>">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fafdf8; margin: 0; color: #1a1a1a; }
        .checkout-container { max-width: 1100px; margin: 40px auto; padding: 0 20px; display: flex; gap: 40px; }
        .form-panel { flex: 1.5; background: #fff; padding: 30px; border-radius: 16px; border: 1px solid rgba(85, 98, 70, 0.08); }
        .summary-panel { flex: 1; background: #fff; padding: 30px; border-radius: 16px; border: 1px solid rgba(85, 98, 70, 0.08); height: fit-content; }
        h2 { color: #5D6247; margin-top: 0; margin-bottom: 20px; font-size: 1.4rem; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 6px; font-size: 0.9rem; font-weight: 500; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; font-family: inherit; }
        .row-group { display: flex; gap: 15px; }
        .item-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.95rem; }
        .promo-box { display: flex; gap: 10px; margin-top: 20px; margin-bottom: 20px; }
        .promo-btn { padding: 12px 20px; background: #5D6247; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
        .pay-btn { width: 100%; padding: 16px; background: #5D6247; color: white; border: none; border-radius: 12px; font-size: 1.1rem; font-weight: 600; cursor: pointer; display: block; text-align: center; margin-top: 20px; box-shadow: 0 4px 15px rgba(93, 98, 71, 0.2); }
        .pay-btn:hover { background: #424d36; }
        .alert { padding: 10px; border-radius: 6px; font-size: 0.85rem; margin-top: 8px; display: none; }
        .alert-success { background: #e6f4ea; color: #137333; }
        .alert-error { background: #fce8e6; color: #c5221f; }

        /* Professional Red Error Alert Banner */
        .error-alert-banner {
            background-color: #fef2f2;
            color: #ef4444;
            border: 1px solid #fca5a5;
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 0.92rem;
            font-weight: 500;
            margin-bottom: 24px;
            display: none;
            align-items: center;
            gap: 12px;
            animation: fadeIn 0.3s ease-in-out;
        }

        .error-alert-banner svg {
            flex-shrink: 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

   <header class="header">
    <a href="<?php echo $base_url; ?>index.php" class="logo">
        <img src="<?php echo $base_url; ?>assets/logo.png" height="60px" alt="Cartify Logo">
    </a>
    
    <nav class="nav-links">
        <a href="<?php echo $base_url; ?>index.php">Home</a>
        <a href="<?php echo $base_url; ?>Pages/About">About</a>
        <a href="<?php echo $base_url; ?>Pages/Contact">Contact</a>
    </nav>

    <div class="nav-icons" style="display: flex; align-items: center; gap: 15px;">
        <?php if ($is_logged_in): ?>
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
            <a href="<?php echo $base_url; ?>Login" aria-label="Login" style="color: inherit; text-decoration: none;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </a>
        <?php endif; ?>

        <!-- CART ICON LINK -->
        <a href="<?php echo $base_url; ?>Products/cart.php" id="cartIconBtn" aria-label="Cart" style="color: inherit; text-decoration: none; position: relative; display: flex; align-items: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
            <?php 
            $cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
            if ($cart_count > 0): 
            ?>
                <span style="position: absolute; top: -8px; right: -10px; background-color: #c62828; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.75rem; font-weight: 700;">
                    <?php echo $cart_count; ?>
                </span>
            <?php endif; ?>
        </a>
    </div>
</header>

    <main class="checkout-container">
        <!-- LEFT: CUSTOMER DETAILS FORM -->
        <section class="form-panel">
            <h2>Shipping & Delivery Details</h2>

            <!-- DYNAMIC RED FORM ERROR BANNER -->
            <div id="checkoutErrorBanner" class="error-alert-banner">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span id="checkoutErrorMessage">Please fill in all standard shipping details before proceeding.</span>
            </div>

            <form id="shippingForm">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" id="custName" class="form-control" placeholder="John Doe" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="custEmail" class="form-control" placeholder="john@example.com" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="tel" id="custPhone" class="form-control" placeholder="9876543210" required>
                </div>
                <div class="form-group">
                    <label>Delivery Address</label>
                    <textarea id="custAddress" class="form-control" rows="3" placeholder="Street address, Apartment, Suite" required></textarea>
                </div>
                <div class="row-group">
                    <div class="form-group" style="flex:1;">
                        <label>City</label>
                        <input type="text" id="custCity" class="form-control" required>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Postal Code</label>
                        <input type="text" id="custPin" class="form-control" required>
                    </div>
                </div>
            </form>
        </section>

        <!-- RIGHT: SUMMARY & DISCOUNTS -->
        <section class="summary-panel">
            <h2>Order Review</h2>
            <?php foreach ($checkout_items as $item): ?>
                <div class="item-row">
                    <span><?php echo htmlspecialchars($item['title']); ?> (x<?php echo $item['quantity']; ?>)</span>
                    <strong>₹<?php echo number_format($item['item_total'], 2); ?></strong>
                </div>
            <?php endforeach; ?>
            <hr style="border:0; border-top:1px solid #eee;">
            
            <!-- PROMO CODE INPUT BLOCK (STRICT AUTO-UPPERCASE) -->
            <div class="promo-box">
                <input type="text" 
                       id="promoCodeInput" 
                       class="form-control" 
                       placeholder="Promo Code (e.g. CARTIFY20)" 
                       style="text-transform: uppercase;" 
                       onkeyup="this.value = this.value.toUpperCase();"
                       onchange="this.value = this.value.toUpperCase();">
                <button type="button" class="promo-btn" onclick="applyPromo()">Apply</button>
            </div>
            <div id="promoAlert" class="alert"></div>

            <div class="item-row" style="margin-top:20px;">
                <span>Subtotal:</span>
                <span id="displaySubtotal">₹<?php echo number_format($total_amount, 2); ?></span>
            </div>
            <div class="item-row" id="discountRow" style="display:none; color:#137333;">
                <span>Promo Discount:</span>
                <span id="displayDiscount">-₹0.00</span>
            </div>
            <div class="item-row" style="font-size:1.2rem; font-weight:700; margin-top:10px;">
                <span>Total Payable:</span>
                <span id="displayTotal">₹<?php echo number_format($total_amount, 2); ?></span>
            </div>

            <!-- Razorpay Custom Trigger Button -->
            <button type="button" class="pay-btn" onclick="initiateRazorpayPayment()">Place Order & Pay</button>
        </section>
    </main>

    <!-- Include Razorpay Checkout Script Overlay Core -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    
    <script>
    let rawSubtotal = <?php echo $total_amount; ?>;
    let finalPayable = rawSubtotal;
    let currentAppliedCode = '';

    // Helper to display professional inline error banner above form
    function showCheckoutError(msg) {
        const errorBanner = document.getElementById('checkoutErrorBanner');
        const errorMessage = document.getElementById('checkoutErrorMessage');
        
        errorMessage.innerText = msg;
        errorBanner.style.display = 'flex';
        
        // Scroll smoothly to top of form panel
        errorBanner.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function hideCheckoutError() {
        document.getElementById('checkoutErrorBanner').style.display = 'none';
    }

    // Async promo code validation
    function applyPromo() {
        const codeInput = document.getElementById('promoCodeInput');
        
        // Enforce UPPERCASE conversion directly on captured JavaScript value
        const code = codeInput.value.trim().toUpperCase();
        codeInput.value = code;
        
        const alertBox = document.getElementById('promoAlert');
        
        if(!code) {
            alertBox.style.display = 'block';
            alertBox.className = 'alert alert-error';
            alertBox.innerText = 'Please enter a promo code first.';
            return;
        }

        alertBox.style.display = 'block';
        alertBox.className = 'alert';
        alertBox.innerText = 'Validating code...';

        fetch('apply-promocode.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `code=${encodeURIComponent(code)}&order_value=${rawSubtotal}`
        })
        .then(res => res.json())
        .then(data => {
            alertBox.style.display = 'block';
            if(data.success) {
                alertBox.className = 'alert alert-success';
                alertBox.innerText = data.message;
                
                let discount = parseFloat(data.discount);
                finalPayable = Math.max(0, rawSubtotal - discount);
                currentAppliedCode = code;

                document.getElementById('discountRow').style.display = 'flex';
                document.getElementById('displayDiscount').innerText = `-₹${discount.toFixed(2)}`;
                document.getElementById('displayTotal').innerText = `₹${finalPayable.toFixed(2)}`;
            } else {
                alertBox.className = 'alert alert-error';
                alertBox.innerText = data.message;

                // Reset calculations on invalid code
                finalPayable = rawSubtotal;
                currentAppliedCode = '';
                document.getElementById('discountRow').style.display = 'none';
                document.getElementById('displayTotal').innerText = `₹${rawSubtotal.toFixed(2)}`;
            }
        })
        .catch(err => {
            alertBox.style.display = 'block';
            alertBox.className = 'alert alert-error';
            alertBox.innerText = 'Server error validating promo code.';
        });
    }

    function initiateRazorpayPayment() {
        hideCheckoutError();

        // 1. Run standard native HTML validation checks
        if(!document.getElementById('shippingForm').checkValidity()) {
            showCheckoutError("Please fill in all standard shipping details before proceeding.");
            return;
        }

        // Capture input fields
        const name = document.getElementById('custName').value;
        const email = document.getElementById('custEmail').value;
        const rawPhone = document.getElementById('custPhone').value;
        const address = document.getElementById('custAddress').value;
        const city = document.getElementById('custCity').value;
        const pincode = document.getElementById('custPin').value;
        
        // Clean formatting from phone numbers
        const cleanPhone = rawPhone.replace(/[^0-9+]/g, '');

        if (cleanPhone.length < 10) {
            showCheckoutError("Please enter a valid phone number (minimum 10 digits).");
            return;
        }

        const fullDeliveryAddress = `${address},\n${city} - ${pincode}`;

        // 2. Save shipping details to session asynchronously before opening Razorpay
        fetch('save-shipping-session.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `name=${encodeURIComponent(name)}&phone=${encodeURIComponent(cleanPhone)}&address=${encodeURIComponent(fullDeliveryAddress)}`
        })
        .then(res => res.json())
        .then(data => {
            
            // 3. Dynamic Calculation Validation
            let calculatedPayable = parseFloat(finalPayable);
            if (isNaN(calculatedPayable) || calculatedPayable <= 0) {
                showCheckoutError("Invalid checkout transaction amount.");
                return;
            }

            // Exact INR Rupee to Paise conversion
            let paymentAmountPaise = Math.round(calculatedPayable * 100); 

            // 4. Configure Razorpay parameters
            var options = {
                "key": "rzp_test_TBkIfbkRPjRRoY", 
                "amount": paymentAmountPaise,
                "currency": "INR",
                "name": "Cartify Marketplace",
                "description": "Store Order Payment",
                "image": "https://cdn-icons-png.flaticon.com/512/1170/1170576.png",
                "handler": function (response){
                    window.location.href = `verify-payment.php?payment_id=${response.razorpay_payment_id}&signature=${response.razorpay_signature}&promo=${currentAppliedCode}`;
                },
                "prefill": {
                    "name": name,
                    "email": email,
                    "contact": cleanPhone
                },
                "theme": { "color": "#5D6247" }
            };
            
            try {
                var rzp1 = new Razorpay(options);
                
                rzp1.on('payment.failed', function (response){
                    showCheckoutError("Razorpay Error: " + response.error.description);
                });
                
                rzp1.open();
            } catch(err) {
                showCheckoutError("Failed to initialize payment overlay: " + err.message);
            }
        })
        .catch(error => {
            showCheckoutError("Session storage handshake failed. Please try again.");
        });
    }
    </script>
</body>
</html>
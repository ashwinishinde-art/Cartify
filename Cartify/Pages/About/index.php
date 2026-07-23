<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define project root path to avoid relative path breaking in subfolders
$base_url = '/Cartify/'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | Cartify</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../header.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

<!-- ================= NAVBAR ================= -->

<header class="header">
    <!-- Logo (Left) -->
    <a href="<?php echo $base_url; ?>index.php" class="logo">
        <img src="<?php echo $base_url; ?>assets/logo.png" alt="Cartify Logo">
    </a>
    
    <!-- Right Navigation Group -->
    <nav class="nav-links">
        <!-- Navigation Links -->
        <div class="standard-links" id="standardLinks">
            <a href="<?php echo $base_url; ?>index.php">Home</a>
            <a href="<?php echo $base_url; ?>Pages/About">About</a>
            <a href="<?php echo $base_url; ?>Pages/Contact">Contact</a>
        </div>

        <!-- Expandable Search Form -->
        <form action="<?php echo $base_url; ?>Products/products.php" method="GET" class="search-container" id="searchForm">
            <div class="search-input-wrapper" id="searchInputWrapper">
                <input type="text" name="search" class="search-input" id="searchInput" placeholder="Search brands, products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" required>
            </div>
        </form>

        <!-- Right Side Icons -->
        <div class="nav-icons">
            <button type="button" class="search-btn" id="searchTriggerBtn" aria-label="Toggle Search">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>

            <?php if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])): ?>
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
                <a href="<?php echo $base_url; ?>Login" aria-label="Login" class="icon-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </a>
            <?php endif; ?>

            <div class="cart-wrapper">
                <!-- Dynamic Cart Link based on Login Status -->
                <?php 
                $cart_url = (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) 
                    ? $base_url . 'Products/cart.php' 
                    : $base_url . 'Login'; 
                ?>
                <a href="<?php echo $cart_url; ?>" class="icon-link" aria-label="Cart">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
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
                    <span class="cart-badge">
                        <?php echo $cart_count; ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<!-- ================= HERO ================= -->

<section class="hero reveal">

    <div class="hero-text">

        <h1>About Cartify</h1>

        <p>
            Your trusted destination for fashion, electronics,
            home essentials and much more.
            We make online shopping simple, secure and enjoyable.
        </p>

        <a href="<?php echo $base_url; ?>index.php" class="btn">
            Shop Now
        </a>

    </div>

    <div class="hero-image">

        <img src="<?php echo $base_url; ?>assets/shopping.jpg" alt="Shopping">

    </div>

</section>

<!-- ================= STORY ================= -->

<section class="story reveal">

    <h2>Our Story</h2>

    <p>

        Cartify started with a simple idea—
        bringing quality products from trusted sellers
        into one convenient platform.

        Instead of visiting multiple websites,
        customers can shop everything they need
        from one place while enjoying affordable
        prices and a seamless shopping experience.

    </p>

</section>

<!-- ================= MISSION ================= -->

<section class="mission reveal">

    <div class="card">

        <i class="fa-solid fa-bullseye"></i>

        <h3>Our Mission</h3>

        <p>

            To provide high-quality products,
            excellent customer service,
            and a safe shopping experience.

        </p>

    </div>

    <div class="card">

        <i class="fa-solid fa-eye"></i>

        <h3>Our Vision</h3>

        <p>

            To become one of the most trusted
            online shopping platforms by making
            quality products accessible to everyone.

        </p>

    </div>

</section>

<!-- ================= WHY CHOOSE US ================= -->

<section class="why reveal">

    <h2>Why Choose Cartify?</h2>

    <div class="features">

        <div class="feature">

            <i class="fa-solid fa-truck-fast"></i>

            <h4>Fast Delivery</h4>

            <p>Quick shipping across India.</p>

        </div>

        <div class="feature">

            <i class="fa-solid fa-lock"></i>

            <h4>Secure Payment</h4>

            <p>100% Safe Transactions.</p>

        </div>

        <div class="feature">

            <i class="fa-solid fa-box"></i>

            <h4>Premium Products</h4>

            <p>Quality checked before delivery.</p>

        </div>

        <div class="feature">

            <i class="fa-solid fa-headset"></i>

            <h4>24/7 Support</h4>

            <p>Always ready to help.</p>

        </div>

    </div>

</section>

<!-- ================= STATS ================= -->

<section class="stats reveal">

    <div class="stat">
        <h2 class="stat-number" data-target="10000">1000</h2>
        <p>Happy Customers</p>
    </div>

    <div class="stat">
        <h2 class="stat-number" data-target="500">50</h2>
        <p>Products</p>
    </div>

    <div class="stat">
        <h2 class="stat-number" data-target="50">25</h2>
        <p>Brands</p>
    </div>

    <div class="stat">
        <h2 class="stat-number" data-target="98">80</h2>
        <p>Positive Reviews (%)</p>
    </div>

</section>

<!-- ================= TEAM ================= -->

<section class="team reveal">

    <h2>Meet Our Team</h2>

    <div class="members">

        <div class="member">
            <div class="avatar"></div>
            <h4>Rohan</h4>
            <p></p>
        </div>

        <div class="member">
            <div class="avatar"></div>
            <h4>Arihant</h4>
            <p></p>
        </div>

        <div class="member">
            <div class="avatar"></div>
            <h4>Soham</h4>
            <p></p>
        </div>

        <div class="member">
            <div class="avatar"></div>
            <h4>Jaideep</h4>
            <p></p>
        </div>

        <div class="member">
            <div class="avatar"></div>
            <h4>Rishikesh</h4>
            <p></p>
        </div>

    </div>

</section>

<!-- ================= CTA ================= -->

<section class="cta">

    <h2>Ready To Shop?</h2>

    <p>
        Discover thousands of products
        carefully selected just for you.
    </p>

    <a href="<?php echo $base_url; ?>Products/explore.php" class="btn">
        Explore Products
    </a>

</section>

<!-- ================= FOOTER ================= -->

<footer class="site-footer">
    <div class="footer-container">
        
        <a href="<?php echo $base_url; ?>index.php" class="footer-logo-brand">
            <img src="<?php echo $base_url; ?>assets/logo.png" height="60px" alt="Cartify Logo">
        </a>
        <div class="footer-columns-wrapper">
            
            <div class="footer-column">
                <h2 class="footer-column-title">Address</h2>
                <p class="footer-address-line">Narhegaon, Pune, Maharashtra 411041</p>
                
                <div class="footer-phone-block">
                    <p>+91 8198 8198 41 ,</p>
                    <p>+91 9511 6799 83</p>
                </div>
            </div>

            <div class="footer-column">
                <h2 class="footer-column-title">Quick Links</h2>
                <ul class="footer-links-list">
                    <li><a href="<?php echo $base_url; ?>index.php">Home</a></li>
                    <li><a href="<?php echo $base_url; ?>Pages/Contact">Contact</a></li>
                    <li><a href="<?php echo $base_url; ?>Pages/About">About Us</a></li>
                    
                    <!-- DYNAMIC REAL-TIME ROLE CHECK WITH ERROR CATCHING -->
                    <li>
                        <?php 
                        $is_active_merchant = false;

                        if (isset($pdo) && (isset($_SESSION['user_id']) || isset($_SESSION['user']['id']))) {
                            $check_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : (int)$_SESSION['user']['id'];
                            
                            try {
                                $roleStmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
                                $roleStmt->execute([$check_id]);
                                $liveRole = $roleStmt->fetchColumn();
                                
                                if ($liveRole === 'merchant') {
                                    $is_active_merchant = true;
                                }
                            } catch (Exception $e) {
                                if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'merchant') {
                                    $is_active_merchant = true;
                                }
                            }
                        }

                        if ($is_active_merchant): 
                        ?>
                            <a href="<?php echo $base_url; ?>Merchant/dashboard.php">Merchant Profile Dashboard</a>
                        <?php else: ?>
                            <a href="<?php echo $base_url; ?>Login">Become a Seller</a>
                        <?php endif; ?>
                    </li>
                    
                    <li><a href="<?php echo $base_url; ?>Pages/Privacy-Policy">Privacy policy</a></li>
                    <li><a href="<?php echo $base_url; ?>Pages/Terms&Conditions">Terms &amp; Conditions</a></li>
                    <li><a href="<?php echo $base_url; ?>Pages/Return">Return</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h2 class="footer-column-title">Social</h2>
                <div class="footer-social-row">
                    <a href="#" class="social-icon-link" aria-label="Facebook">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="16" cy="16" r="16" fill="white"/>
                            <path d="M19 16h-2.5v9h-3.5v-9h-2v-3h2V11.2c0-2.5 1.2-3.7 3.8-3.7h2.7v3h-1.7c-1.1 0-1.3.5-1.3 1.2V13H19l-0.5 3z" fill="#556246"/>
                        </svg>
                    </a>
                    
                    <a href="#" class="social-icon-link" aria-label="Instagram">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="16" cy="16" r="16" fill="white"/>
                            <rect x="7" y="7" width="18" height="18" rx="4.5" stroke="#556246" stroke-width="2"/>
                            <circle cx="16" cy="16" r="4" stroke="#556246" stroke-width="2"/>
                            <circle cx="21" cy="11" r="1" fill="#556246"/>
                        </svg>
                    </a>
                    
                    <a href="#" class="social-icon-link" aria-label="LinkedIn">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="32" height="32" rx="6" fill="white"/>
                            <path d="M7 11h4v13H7V11zm2-5a2 2 0 1 1 0 4 2 2 0 0 1 0-4zm7 5h3.8v1.8h0.1c0.5-1 1.9-2 3.8-2 4 0 4.8 2.5 4.8 6v7.2h-4v-6.2c0-1.5-.1-3.4-2.1-3.4-2 0-2.4 1.6-2.4 3.3V24h-4V11z" fill="#556246"/>
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</footer>

<script>
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

    document.addEventListener('click', function(e) {
        const container = document.querySelector('.nav-links');
        const trigger = document.getElementById('searchTriggerBtn');
        const wrapper = document.getElementById('searchInputWrapper');
        const links = document.getElementById('standardLinks');
        
        if (container && !container.contains(e.target) && !trigger.contains(e.target)) {
            wrapper.classList.remove('active');
            if (links) links.style.opacity = '1';
        }
    });
</script>

</body>
</html>
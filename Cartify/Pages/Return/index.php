<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define project root path to ensure all navbar, footer, and asset links resolve cleanly
$base_url = '/Cartify/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Policy | Cartify</title>
    <link rel="stylesheet" href="../header.css">
    <style>
/* ===================================================
   Cartify — Policy Pages (Privacy / Terms / Returns)
   Palette sampled directly from the homepage screenshot:
   - Olive (sofa / footer)   #5E624A
   - Near-black (button/ink) #1E1E1E
   - Body gray               #606060
   - Card gray (deals box)   #F1F0EE
   - Testimonial green tint  #E3F1D1
=================================================== */

:root {
    --olive: #5E624A;
    --olive-dark: #4A4D3A;
    --ink: #1E1E1E;
    --ink-gray: #606060;
    --card-gray: #F1F0EE;
    --tint-green: #E3F1D1;
    --line: #E8E6E0;
    --white: #FFFFFF;
    --radius: 10px;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: var(--white);
    color: var(--ink);
    line-height: 1.7;
    -webkit-font-smoothing: antialiased;
}

a {
    text-decoration: none;
    color: inherit;
}

ul {
    list-style: none;
}

/* ================= NAVBAR ================= */

header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 26px 60px;
    background: var(--white);
}

.logo a {
    font-size: 20px;
    font-weight: 600;
    letter-spacing: 0.3px;
    color: var(--ink);
}
.nav-logo {
    width: 200px;
    height: auto;
}
.footer-logo-img {
    width: 140px;
    height: auto;
}
nav {
    display: flex;
    gap: 42px;
}

nav a {
    font-size: 14px;
    font-weight: 400;
    color: var(--ink);
    transition: color 0.2s ease;
}

nav a:hover,
nav a.active {
    color: var(--olive);
}

.icons {
    display: flex;
    gap: 22px;
    font-size: 16px;
    color: var(--ink);
}

/* ================= PAGE HERO ================= */

.policy-hero {
    padding: 60px 60px 44px;
    text-align: left;
    max-width: 1140px;
    margin: 0 auto;
}

.policy-hero .eyebrow {
    display: inline-block;
    font-size: 12px;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: var(--olive);
    font-weight: 600;
    margin-bottom: 14px;
}

.policy-hero h1 {
    font-size: 38px;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 14px;
    line-height: 1.25;
}

.policy-hero p {
    max-width: 560px;
    color: var(--ink-gray);
    font-size: 15px;
}

.policy-hero .updated {
    display: inline-block;
    margin-top: 20px;
    font-size: 13px;
    color: var(--ink-gray);
    background: var(--card-gray);
    padding: 6px 16px;
    border-radius: 999px;
}

/* ================= LAYOUT ================= */

.policy-wrap {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 60px;
    max-width: 1140px;
    margin: 0 auto 70px;
    padding: 0 60px;
    align-items: start;
}

/* ---- Sidebar TOC ---- */

.policy-toc {
    position: sticky;
    top: 30px;
    background: var(--card-gray);
    border-radius: 16px;
    padding: 26px 22px;
}

.policy-toc h4 {
    font-size: 11px;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: var(--olive);
    margin-bottom: 16px;
    font-weight: 600;
}

.policy-toc li {
    margin-bottom: 2px;
}

.policy-toc a {
    display: block;
    font-size: 14px;
    color: var(--ink-gray);
    padding: 8px 10px;
    border-radius: 8px;
    transition: all 0.15s ease;
}

.policy-toc a:hover {
    background: var(--white);
    color: var(--ink);
}

.policy-toc .num {
    display: inline-block;
    width: 20px;
    color: var(--olive);
    font-weight: 600;
}

/* ---- Content ---- */

.policy-content section {
    margin-bottom: 42px;
    scroll-margin-top: 30px;
}

.policy-content h2 {
    font-size: 21px;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 14px;
    display: flex;
    align-items: baseline;
    gap: 10px;
}

.policy-content h2 .step {
    font-size: 13px;
    font-weight: 600;
    color: var(--olive);
}

.policy-content p {
    color: var(--ink-gray);
    font-size: 15px;
    margin-bottom: 14px;
}

.policy-content ul.bullets {
    margin: 10px 0 18px 4px;
}

.policy-content ul.bullets li {
    position: relative;
    padding-left: 22px;
    margin-bottom: 10px;
    color: var(--ink-gray);
    font-size: 15px;
}

.policy-content ul.bullets li::before {
    content: "";
    position: absolute;
    left: 0;
    top: 9px;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--olive);
}

.policy-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 10px 0 20px;
    font-size: 14px;
    background: var(--card-gray);
    border-radius: var(--radius);
    overflow: hidden;
}

.policy-content th,
.policy-content td {
    text-align: left;
    padding: 13px 16px;
    border-bottom: 1px solid var(--white);
    color: var(--ink-gray);
}

.policy-content th {
    font-weight: 600;
    background: var(--olive);
    color: var(--white);
}

.callout {
    background: var(--tint-green);
    padding: 16px 20px;
    border-radius: var(--radius);
    font-size: 14px;
    color: #3E4A2E;
    margin-bottom: 18px;
}

.contact-card {
    background: var(--olive);
    color: var(--white);
    border-radius: 16px;
    padding: 34px;
    margin-top: 10px;
}

.contact-card h3 {
    font-size: 19px;
    margin-bottom: 8px;
}

.contact-card p {
    color: #E4E5D8;
    font-size: 14px;
    margin-bottom: 20px;
}

.contact-card .btn {
    display: inline-block;
    background: var(--ink);
    color: var(--white);
    padding: 12px 26px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    transition: opacity 0.2s ease;
}

.contact-card .btn:hover {
    opacity: 0.85;
}

/* ================= FOOTER ================= */

.site-footer {
    background-color: #5D6247;
    color: #ffffff;
    padding: 70px 8% 90px;
    box-sizing: border-box;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 50px;
}

.footer-logo-brand {
    font-family: 'Just Another Hand', cursive;
    font-size: 3.5rem;
    color: #ffffff;
    text-align: left;
    font-weight: 400;
    line-height: 1;
}

.footer-columns-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 40px;
}

.footer-column {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.footer-column-title {
    font-family: 'Fredoka', sans-serif;
    font-size: 1.4rem;
    font-weight: 600;
    margin: 0 0 24px 0;
    letter-spacing: 0.2px;
}

.footer-address-line {
    font-family: 'Fredoka', sans-serif;
    font-weight: 400;
    font-size: 1.1rem;
    line-height: 1.5;
    max-width: 300px;
    margin: 0 0 24px 0;
}

.footer-phone-block {
    font-family: 'Fredoka', sans-serif;
    font-weight: 300;
    font-size: 1.1rem;
    line-height: 1.6;
}

.footer-phone-block p {
    margin: 0;
}

.footer-links-list {
    list-style: none;
    padding: 0;
    margin: 0;
    line-height: 1.21;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.footer-links-list a {
    font-family: 'Fredoka', sans-serif;
    font-weight: 400;
    color: #ffffff;
    text-decoration: none;
    font-size: 1.1rem;
    transition: opacity 0.2s ease;
}

.footer-links-list a:hover {
    opacity: 0.8;
}

.footer-social-row {
    display: flex;
    gap: 20px;
    align-items: center;
}

.social-icon-link {
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s ease;
}

.social-icon-link:hover {
    transform: scale(1.08);
}

@media (max-width: 768px) {
    .footer-columns-wrapper {
        flex-direction: column;
        gap: 40px;
    }
    
    .site-footer {
        padding: 50px 6% 60px;
    }
    
    .footer-logo-brand {
        font-size: 3rem;
    }
}

@media (max-width: 900px) {
    .policy-wrap {
        grid-template-columns: 1fr;
    }
    .policy-toc {
        position: static;
    }
    header, .policy-hero, .policy-wrap {
        padding-left: 24px;
        padding-right: 24px;
    }
    footer {
        grid-template-columns: 1fr;
        padding: 40px 24px;
    }
}

@media (max-width: 640px) {
    nav {
        display: none;
    }
    .policy-hero h1 {
        font-size: 28px;
    }
}
    </style>

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

<section class="policy-hero">
    <span class="eyebrow">Legal</span>
    <h1>Return Policy</h1>
    <p>Not the right fit? Here's how easy it is to return or exchange items bought on Cartify.</p>
    <span class="updated">Last updated: July 18, 2026</span>
</section>

<!-- ================= CONTENT ================= -->

<div class="policy-wrap">

    <!-- TOC -->
    <aside class="policy-toc">
        <h4>On this page</h4>
        <ul>
            <li><a href="#introduction"><span class="num">01</span>Introduction</a></li>
            <li><a href="#eligibility"><span class="num">02</span>Return Eligibility</a></li>
            <li><a href="#window"><span class="num">03</span>Return Window</a></li>
            <li><a href="#non-returnable"><span class="num">04</span>Non-Returnable Items</a></li>
            <li><a href="#how-to"><span class="num">05</span>How to Initiate a Return</a></li>
            <li><a href="#refunds"><span class="num">06</span>Refunds</a></li>
            <li><a href="#exchanges"><span class="num">07</span>Exchanges</a></li>
            <li><a href="#damaged"><span class="num">08</span>Damaged or Defective Items</a></li>
            <li><a href="#contact"><span class="num">09</span>Contact Us</a></li>
        </ul>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="policy-content">

        <section id="introduction">
            <h2><span class="step">01</span> Introduction</h2>
            <p>
                We want you to love what you order. If something isn't right, our
                return policy makes it simple to send items back for a refund or
                exchange, subject to the conditions below.
            </p>
        </section>

        <section id="eligibility">
            <h2><span class="step">02</span> Return Eligibility</h2>
            <p>To be eligible for a return, items must be:</p>
            <ul class="bullets">
                <li>Unused, unwashed, and in their original condition.</li>
                <li>In the original packaging with all tags attached.</li>
                <li>Accompanied by the original invoice or order confirmation.</li>
            </ul>
        </section>

        <section id="window">
            <h2><span class="step">03</span> Return Window</h2>
            <table>
                <tr><th>Category</th><th>Return Window</th></tr>
                <tr><td>Fashion &amp; Accessories</td><td>7 days from delivery</td></tr>
                <tr><td>Electronics</td><td>10 days from delivery</td></tr>
                <tr><td>Home Essentials</td><td>7 days from delivery</td></tr>
                <tr><td>Instruments &amp; Sports</td><td>10 days from delivery</td></tr>
            </table>
            <div class="callout">
                Return requests raised after the applicable window cannot be
                processed, so we recommend checking your order as soon as it arrives.
            </div>
        </section>

        <section id="non-returnable">
            <h2><span class="step">04</span> Non-Returnable Items</h2>
            <p>The following items cannot be returned, unless received damaged or defective:</p>
            <ul class="bullets">
                <li>Perishable items, including groceries.</li>
                <li>Personal care and hygiene products.</li>
                <li>Items marked "Final Sale" at checkout.</li>
                <li>Gift cards.</li>
            </ul>
        </section>

        <section id="how-to">
            <h2><span class="step">05</span> How to Initiate a Return</h2>
            <ul class="bullets">
                <li>Go to <strong>My Orders</strong> and select the item you'd like to return.</li>
                <li>Choose a reason for the return and select "Request Return."</li>
                <li>Pack the item securely with all original tags and packaging.</li>
                <li>Hand it over to our courier partner when they arrive for pickup.</li>
            </ul>
        </section>

        <section id="refunds">
            <h2><span class="step">06</span> Refunds</h2>
            <p>
                Once we receive and inspect your returned item, refunds are processed
                to your original payment method within 5–7 business days. For
                Cash-on-Delivery orders, refunds are issued to your bank account or as
                Cartify store credit, whichever you prefer.
            </p>
        </section>

        <section id="exchanges">
            <h2><span class="step">07</span> Exchanges</h2>
            <p>
                Need a different size or colour? Select "Exchange" instead of
                "Return" when raising your request, and we'll ship the replacement
                once the original item is picked up, subject to availability.
            </p>
        </section>

        <section id="damaged">
            <h2><span class="step">08</span> Damaged or Defective Items</h2>
            <p>
                If you receive a damaged, defective, or incorrect item, please reach
                out to us within 48 hours of delivery with photos of the product. We
                will arrange a free replacement or full refund — no return shipping
                cost to you.
            </p>
        </section>

        <section id="contact">
            <h2><span class="step">09</span> Contact Us</h2>
            <div class="contact-card">
                <h3>Need help with a return?</h3>
                <p>Our support team can guide you through the process, start to finish.</p>
                <a href="<?php echo $base_url; ?>Pages/Contact" class="btn">Contact Support</a>
            </div>
        </section>

    </main>
</div>

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
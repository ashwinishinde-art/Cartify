<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartify - Home</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Frank+Ruhl+Libre:wght@400;500&family=Inter:wght@400;500&family=Just+Another+Hand&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Monda:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
</head>
<body>

    <?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>

<header class="header">
    <a href="index.php" class="logo"><img src="../assets/logo.png" height="60px"></a>
    
    <nav class="nav-links">
        <div class="nav-menu-wrapper" style="display: flex; align-items: center; gap: 20px;">
            <div class="standard-links" id="standardLinks">
                <a href="../index.php">Home</a>
                <a href="../About/about.html">About</a>
                <a href="">Contact</a>
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
                    <a href="Products/track-orders.php">Track Order</a>
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



    <main class="hero">
        <div class="hero-text">
            <h1>Everything You Need,<br>All in One Cart.</h1>
            <p>Shop thousands of products with secure checkout and quick delivery. Cartify is here to fulfill.</p>
            <a href="#" class="btn-explore">EXPLORE</a>
        </div>
        
        <div class="hero-visual">
            <img src="Assets/hero-img.png" alt="Green sofa and clothing rack">
        </div>
    </main>

    <section class="categories-section">
        <h2 class="categories-title">Popular Categories</h2>
        
        <div class="categories-container">
            <div class="category-item">
                <div class="category-img-wrapper">
                    <a href="http://localhost/Cartify/Products/products.php?search=fashion"><img src="Assets/Fasion.png" alt="Fashion"></a>
                </div>
                <span class="category-label">Fashion</span>
            </div>

            <div class="category-item">
                <div class="category-img-wrapper">
                    <img src="Assets/home.png" alt="Home">
                </div>
                <span class="category-label">Home</span>
            </div>

            <div class="category-item">
                <div class="category-img-wrapper">
                    <img src="Assets/Instruments.webp" alt="Instruments">
                </div>
                <span class="category-label">Instruments</span>
            </div>

            <div class="category-item">
                <div class="category-img-wrapper">
                    <img src="Assets/accessories.png" alt="Accessories">
                </div>
                <span class="category-label">Accessories</span>
            </div>

            <div class="category-item">
                <div class="category-img-wrapper">
                    <img src="Assets/Electronics.jpg" alt="Electronics">
                </div>
                <span class="category-label">Electronics</span>
            </div>

            <div class="category-item">
                <div class="category-img-wrapper">
                    <img src="Assets/sports.png" alt="Sports">
                </div>
                <span class="category-label">Sports</span>
            </div>

            <div class="category-item">
                <div class="category-img-wrapper">
                    <img src="Assets/Groceries.jpeg" alt="Groceries">
                </div>
                <span class="category-label">Groceries</span>
            </div>
        </div>
    </section>
    

<section class="deals-section">
    <div class="deals-container">
        <h2 class="deals-title">Deals of the Day</h2>
        
        <div class="deals-grid">
            <div class="deal-card tall-card">
                <img src="Assets/Casual-Wear.png" alt="Casual Wear Deal">
            </div>
            
           <div class="deals-right-column">
                <div class="deal-card tech-card">
                    <img src="Assets/tech-deal.png" alt="Laptop and Mobile Deals">
                </div>
                
                <div class="deal-card wide-card">
                    <img src="Assets/Guitar.png" alt="Guitar Deal">
                </div>
            </div>
        </div>
    </div>
</section>
<section class="products-section">
        <h2 class="products-title">Popular Products</h2>
        
        <div class="products-grid">
            <div class="product-card">
                <div class="product-img-wrapper">
                    <img src="Assets/striped-shirt.png" alt="Vertical Striped Shirt">
                </div>
                <h3 class="product-title">Vertical Striped Shirt</h3>
                <div class="product-rating">
                    <div class="stars">★★★★★</div>
                    <span class="rating-text">5.0/5</span>
                </div>
                <div class="product-price-row">
                    <span class="current-price">$212</span>
                    <span class="old-price">$232</span>
                    <span class="discount-tag">-20%</span>
                </div>
            </div>

            <div class="product-card">
                <div class="product-img-wrapper">
                    <img src="Assets/graphic-tshirt.png" alt="Courage Graphic T-shirt">
                </div>
                <h3 class="product-title">Courage Graphic T-shirt</h3>
                <div class="product-rating">
                    <div class="stars">★★★★☆</div>
                    <span class="rating-text">4.0/5</span>
                </div>
                <div class="product-price-row">
                    <span class="current-price">$145</span>
                </div>
            </div>

            <div class="product-card">
                <div class="product-img-wrapper">
                    <img src="Assets/shorts.png" alt="Loose Fit Bermuda Shorts">
                </div>
                <h3 class="product-title">Loose Fit Bermuda Shorts</h3>
                <div class="product-rating">
                    <div class="stars">★★★☆☆</div>
                    <span class="rating-text">3.0/5</span>
                </div>
                <div class="product-price-row">
                    <span class="current-price">$80</span>
                </div>
            </div>

            <div class="product-card">
                <div class="product-img-wrapper">
                    <img src="Assets/jeans.png" alt="Faded Skinny Jeans">
                </div>
                <h3 class="product-title">Faded Skinny Jeans</h3>
                <div class="product-rating">
                    <div class="stars">★★★★☆</div>
                    <span class="rating-text">4.5/5</span>
                </div>
                <div class="product-price-row">
                    <span class="current-price">$210</span>
                </div>
            </div>
        </div>
    </section>
<section class="why-shop-section">
        <h2 class="why-shop-title">Why Shop With Cartify</h2>
        
        <div class="why-shop-content">
            <div class="why-shop-visual">
                <img src="Assets/bag.png" alt="Cartify Shopping Bag">
            </div>
            
            <div class="why-shop-text">
                <p>At Cartify, we're committed to making online shopping effortless, secure, and enjoyable. From fashion and electronics to home essentials and everyday must-haves, we offer a carefully curated selection of quality products at competitive prices. With a user-friendly shopping experience, secure payment options, and reliable delivery, you can shop with confidence knowing your satisfaction is always our priority.</p>
                <p>We believe great shopping goes beyond just buying products. That's why Cartify offers fast shipping, hassle-free returns, exclusive deals, and dedicated customer support to ensure every purchase is smooth from start to finish. Whether you're discovering new favorites or shopping for daily essentials, Cartify is your trusted destination for quality, convenience, and exceptional value.</p>
            </div>
        </div>
    </section>
    <section class="reviews-section">
        <h2 class="reviews-title">What Our Customers Are Saying!</h2>
        <p class="reviews-subtitle">Pure, natural, and trusted turmeric loved by customers for its rich flavor, freshness, and everyday wellness.</p>
        
        <div class="carousel-container">
            <button class="arrow-btn prev-btn" id="prevBtn" aria-label="Previous Slide">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            </button>

            <div class="carousel-window">
                <div class="reviews-track" id="reviewsTrack">
                    <div class="review-card">
                        <h3 class="review-card-title">Pure Quality You Can Trust</h3>
                        <p class="review-body">Shopping on Cartify was an amazing experience from start to finish. The website is incredibly easy to navigate, making it simple to find exactly what I was looking for. My order was processed quickly.</p>
                        <span class="review-author">-Rishikesh Shirsat</span>
                    </div>

                    <div class="review-card">
                        <h3 class="review-card-title">Pure Quality You Can Trust</h3>
                        <p class="review-body">Shopping on Cartify was an amazing experience from start to finish. The website is incredibly easy to navigate, making it simple to find exactly what I was looking for. My order was processed quickly.</p>
                        <span class="review-author">-Soham Sonawane</span>
                    </div>

                    <div class="review-card">
                        <h3 class="review-card-title">Pure Quality You Can Trust</h3>
                        <p class="review-body">Shopping on Cartify was an amazing experience from start to finish. The website is incredibly easy to navigate, making it simple to find exactly what I was looking for. My order was processed quickly.</p>
                        <span class="review-author">-Arihant Pokharna</span>
                    </div>

                    <div class="review-card">
                        <h3 class="review-card-title">Fast Shipping & Support</h3>
                        <p class="review-body">I was genuinely blown away by how quickly my delivery arrived! The items were securely packaged and exactly as described. Customer support helped me adjust my address instantly after purchasing.</p>
                        <span class="review-author">-Rohan Mahindrakar</span>
                    </div>

                    <div class="review-card">
                        <h3 class="review-card-title">Highly Recommended!</h3>
                        <p class="review-body">Finding a store with both stellar pricing and elite item quality is rare. Cartify has completely updated how I handle shopping for my daily household essentials. Superb value all around.</p>
                        <span class="review-author">-Jaideep Patil</span>
                    </div>

                    <div class="review-card">
                        <h3 class="review-card-title">Flawless User Interface</h3>
                        <p class="review-body">The absolute smoothest checkout system I've ever experienced online. Zero lag, multiple verified secure checkout options, and clean categorical tracking. Will absolutely buy again soon.</p>
                        <span class="review-author">-Rohan Mehta</span>
                    </div>
                </div>
            </div>

            <button class="arrow-btn next-btn" id="nextBtn" aria-label="Next Slide">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>
        </div>
    </section>
<footer class="site-footer">
        <div class="footer-container">
            <div class="footer-logo-brand">Cartify</div>
            
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
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">About Us</a></li>
                       <!-- Inside your Footer / Navigation Link Stack -->
<li><?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'merchant'): ?>
    <!-- If already a logged-in merchant, fast-track them to their dashboard profile -->
    <a href="/Cartify/Merchant/dashboard.php">Merchant Profile Dashboard</a>
<?php else: ?>
    <!-- If they are a buyer or guest, take them to the onboarding form -->
    <a href="/Cartify/Merchant/">Become a Seller</a>
<?php endif; ?></li>
                        <li><a href="#">Privacy policy</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">Return</a></li>
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
                                <rect x="2" y="2" width="28" height="28" rx="7" stroke="white" stroke-width="2.5"/>
                                <circle cx="16" cy="16" r="6" stroke="white" stroke-width="2.5"/>
                                <circle cx="23.5" cy="8.5" r="1.5" fill="white"/>
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
    <script src="script.js"></script>
</body>
</html>

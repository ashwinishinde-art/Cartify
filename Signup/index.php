<?php
session_start();

// Catch error message from URL parameters
$error_message = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'email_exists') {
        $error_message = 'This email address is already registered. Please log in instead.';
    } elseif ($_GET['error'] === 'empty_fields') {
        $error_message = 'Please fill in all required fields to create your account.';
    } elseif ($_GET['error'] === 'db_error') {
        $error_message = 'A system error occurred. Please try again later.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Cartify</title>
    <link rel="stylesheet" href="../header.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Red Professional Error Banner Styling */
        .error-alert-banner {
            background-color: #fef2f2;
            color: #ef4444;
            border: 1px solid #fca5a5;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
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
     <a href="../index.php" class="logo"><img src="../assets/logo.png" height="60px" alt="Cartify Logo"></a>
    
    <nav class="nav-links">
        <a href="../index.php">Home</a>
        <a href="../Pages/About">About</a>
        <a href="../Pages/Contact">Contact</a>
    </nav>

    <div class="nav-icons">
        <a href="../Login/index.php" aria-label="Login" style="color: inherit; text-decoration: none;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
        </a>

        <!-- CART ICON WITH ID INTERCEPT -->
        <a href="#" id="cartIconBtn" aria-label="Cart" style="color: inherit; text-decoration: none;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
        </a>
    </div>
</header>
  
    <main class="signup-page-wrapper">
        <div class="auth-container">
            <div class="auth-card">
                 <a href="../index.php" class="auth-logo"><img src="../assets/logo.png" height="60px" alt="Cartify Logo"></a>
                
                <h1 class="auth-title">Create Account</h1>
                <p class="auth-subtitle">Join us today to access exclusive member discounts.</p>
                
                <!-- CONTAINER FOR JS & PHP ALERTS -->
                <div id="alertContainer">
                    <?php if (!empty($error_message)): ?>
                        <div class="error-alert-banner">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span><?php echo htmlspecialchars($error_message); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <form id="signupForm" class="auth-form" action="signup-process.php" method="POST">
                    <div class="input-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" placeholder="John Doe" required autocomplete="name">
                    </div>
                    
                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="email@example.com" required autocomplete="email">
                    </div>
                    
                    <div class="input-group">
                        <label for="password">Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="new-password">
                            
                            <!-- DYNAMIC EYE BUTTON -->
                            <button type="button" class="password-toggle" id="passwordToggle" aria-label="Toggle password visibility">
                                <svg id="eyeIcon" class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-utilities">
                        <label class="checkbox-container">
                            <input type="checkbox" name="agree" required>
                            <span class="custom-checkmark"></span>
                            I agree to the <a href="../Pages/Terms&Conditions" class="terms-link">Terms & Conditions</a>
                        </label>
                    </div>
                    
                    <button type="submit" class="submit-btn">Create Account</button>
                </form>
                
                <div class="auth-footer">
                    <p>Already have an account? <a href="../Login/index.php">Log in here</a></p>
                </div>
            </div>
        </div>
    </main>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('passwordToggle');
        const eyeIcon = document.getElementById('eyeIcon');
        const cartIconBtn = document.getElementById('cartIconBtn');
        const alertContainer = document.getElementById('alertContainer');

        // SVG Path definitions for open and closed/slashed eye
        const openEyeSVG = `
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
        `;

        const closedEyeSVG = `
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
            <line x1="1" y1="1" x2="23" y2="23"></line>
        `;
        
        // Password Visibility Toggle
        passwordToggle.addEventListener('click', () => {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            
            if (isPassword) {
                passwordInput.setAttribute('type', 'text');
                eyeIcon.innerHTML = closedEyeSVG;
            } else {
                passwordInput.setAttribute('type', 'password');
                eyeIcon.innerHTML = openEyeSVG;
            }
        });

        // Cart Click Interceptor
        cartIconBtn.addEventListener('click', (e) => {
            e.preventDefault();
            alertContainer.innerHTML = `
                <div class="error-alert-banner">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span>Please Login First to access your cart.</span>
                </div>
            `;
        });
    </script>
</body>
</html>
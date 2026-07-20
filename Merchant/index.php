<?php
// Merchant/index.php
require_once '../db.php'; 
session_start();

// 1. Force check: User must be logged in to access the onboarding steps
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/index.html");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// 2. LIVE STATUS INTERCEPT: Check if their merchant status was revoked by an admin
try {
    $statusCheck = $pdo->prepare("SELECT merchant_status FROM users WHERE id = ?");
    $statusCheck->execute([$user_id]);
    $currentStatus = $statusCheck->fetchColumn();

    if ($currentStatus === 'revoked') {
        // Render the professional restriction screen and freeze further rendering
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Application Restricted - Cartify</title>
            <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
            <style>
                body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
                .error-card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); width: 100%; max-width: 480px; text-align: center; border: 1px solid #e2e8f0; }
                .icon-shield { background: #fef2f2; color: #ef4444; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px auto; font-size: 28px; font-weight: bold; }
                h2 { color: #1e293b; margin: 0 0 12px 0; font-size: 1.5rem; font-weight: 700; }
                p { color: #64748b; font-size: 0.95rem; line-height: 1.6; margin: 0 0 28px 0; }
                .back-btn { display: inline-block; padding: 12px 24px; background: #556246; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 0.9rem; transition: background 0.2s; }
                .back-btn:hover { background: #455038; }
            </style>
        </head>
        <body>
            <div class="error-card">
                <div class="icon-shield">✕</div>
                <h2>Access Privileges Suspended</h2>
                <p>Your merchant onboarding privileges for this account have been explicitly revoked by the system administration due to a violation of our marketplace seller policies.</p>
                <a href="/Cartify/index.php" class="back-btn">Return to Storefront</a>
            </div>
        </body>
        </html>
        <?php
        exit; // Halts compilation immediately so steps 1, 2, or 3 never show
    }
} catch (PDOException $e) {
    // Fails safely if query has initialization anomalies
}

// 3. Fast track active merchants away from the registration forms
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'merchant') {
    header("Location: dashboard.php");
    exit;
}

// 4. Determine which step is active (Defaults to step 1)
$current_step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

// 5. Adjust progress line completion width dynamically (3 steps now)
$fill_width = "0%";
if ($current_step == 2) $fill_width = "50%";
if ($current_step == 3) $fill_width = "100%";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become a Seller - Cartify</title>
    <link rel="stylesheet" href="../header.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fafdf8;
            margin: 0;
            color: #1a1a1a;
        }

        /* --- Multi-Step Onboarding Progress Bar --- */
        .progress-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            max-width: 650px;
            margin: 40px auto 20px;
            padding: 0 20px;
        }

        .progress-container::before {
            content: '';
            background-color: #e0e0e0;
            position: absolute;
            top: 20px;
            left: 40px;
            right: 40px;
            height: 4px;
            z-index: 1;
        }

        .progress-line-fill {
            position: absolute;
            top: 20px;
            left: 40px;
            height: 4px;
            background-color: #5D6247;
            z-index: 2;
            transition: width 0.4s ease;
        }

        .step-node {
            position: relative;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 140px;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #ffffff;
            border: 3px solid #e0e0e0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 600;
            color: #a0a899;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .step-label {
            margin-top: 10px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #888888;
            line-height: 1.3;
        }

        .step-node.active .step-circle {
            border-color: #5D6247;
            color: #5D6247;
            box-shadow: 0 0 0 4px rgba(93, 98, 71, 0.15);
        }

        .step-node.completed .step-circle {
            background-color: #5D6247;
            border-color: #5D6247;
            color: #ffffff;
        }

        .step-node.active .step-label,
        .step-node.completed .step-label {
            color: #5D6247;
            font-weight: 600;
        }

        /* --- Onboarding Form Card --- */
        .merchant-onboarding {
            max-width: 600px;
            margin: 30px auto 60px;
            background: #ffffff;
            padding: 40px;
            border-radius: 32px;
            box-shadow: 0 12px 36px rgba(85, 98, 70, 0.05);
            border: 1px solid rgba(85, 98, 70, 0.08);
        }
        .merchant-onboarding h2 {
            font-size: 1.8rem;
            color: #5D6247;
            text-align: center;
            margin-bottom: 24px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: 500;
            color: #5D6247;
            font-size: 0.95rem;
        }
        .form-group input, .form-group textarea, .form-group select {
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 14px;
            outline: none;
            background-color: #fafdf8;
            transition: border-color 0.2s, background-color 0.2s;
            width: 100%;
            color: #1a1a1a;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            border-color: #5D6247;
            background-color: #ffffff;
        }
        .form-group input:disabled {
            background-color: #f1f5ed;
            border-color: #e0e0e0;
            color: #777777;
            cursor: not-allowed;
        }
        .btn-submit {
            width: 100%;
            background-color: #5D6247;
            color: #ffffff;
            border: none;
            padding: 16px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 16px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(85, 98, 70, 0.2);
            transition: background-color 0.2s, transform 0.2s;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background-color: #424d36;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<header class="header">
    <a href="../index.php" class="logo">Cartify</a>
    <nav class="nav-links">
        <a href="../index.php">Home</a>
        <a href="#">About</a>
        <a href="#">Contact</a>
    </nav>
</header>

    <div class="progress-container">
        <div class="progress-line-fill" style="width: <?php echo $fill_width; ?>;"></div>

        <div class="step-node <?php echo ($current_step == 1) ? 'active' : (($current_step > 1) ? 'completed' : ''); ?>">
            <div class="step-circle"><?php echo ($current_step > 1) ? '✓' : '1'; ?></div>
            <div class="step-label">Account Creation</div>
        </div>

        <div class="step-node <?php echo ($current_step == 2) ? 'active' : (($current_step > 2) ? 'completed' : ''); ?>">
            <div class="step-circle"><?php echo ($current_step > 2) ? '✓' : '2'; ?></div>
            <div class="step-label">Shipping & Pickup</div>
        </div>

        <div class="step-node <?php echo ($current_step == 3) ? 'active' : ''; ?>">
            <div class="step-circle">3</div>
            <div class="step-label">Bank Details</div>
        </div>
    </div>

    <main class="merchant-onboarding">
        <form action="become-merchant.php" method="POST">
            <input type="hidden" name="step" value="<?php echo $current_step; ?>">

            <?php if ($current_step === 1): ?>
                <h2>Seller Account Details</h2>
                
                <div class="form-group">
                    <label>Registered Full Name</label>
                    <input type="text" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Account Email Address (Unchangeable)</label>
                    <input type="email" value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : 'Linked Account Email'; ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="store_name">Store / Brand Name</label>
                    <input type="text" id="store_name" name="store_name" placeholder="e.g. John's Boutique" required>
                </div>

                <div class="form-group">
                    <label for="phone_number">Seller Mobile Number</label>
                    <input type="tel" id="phone_number" name="phone_number" placeholder="e.g. +91 XXXXX XXXXX" required>
                </div>
                
                <button type="submit" class="btn-submit">Save & Continue</button>

            <?php elseif ($current_step === 2): ?>
                <h2>Shipping & Pickup Preferences</h2>
                <div class="form-group">
                    <label for="business_address">Store Pickup Address</label>
                    <textarea id="business_address" name="business_address" rows="4" placeholder="Enter your warehouse or store pickup location address" required></textarea>
                </div>
                <div class="form-group">
                    <label for="shipping_method">Preferred Shipping Partner Mode</label>
                    <select id="shipping_method" name="shipping_method" required>
                        <option value="Self-Ship">Merchant Self-Ship</option>
                        <option value="Cartify-Fulfill">Cartify Express Logistics</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Save & Continue</button>

            <?php elseif ($current_step === 3): ?>
                <h2>Bank Account Credentials</h2>
                <div class="form-group">
                    <label for="bank_account_no">Bank Account Number</label>
                    <input type="text" id="bank_account_no" name="bank_account_no" placeholder="Enter bank account no" required>
                </div>
                <div class="form-group">
                    <label for="bank_ifsc">IFSC / Routing Code</label>
                    <input type="text" id="bank_ifsc" name="bank_ifsc" placeholder="e.g. SBIN0012345" required>
                </div>
                <button type="submit" class="btn-submit">Complete Setup & Activate Shop</button>
            <?php endif; ?>
        </form>
    </main>

</body>
</html>
<?php
// Admin/index.php
require_once '../db.php';
session_start();

// 1. ANONYMOUS CHECK: If not logged in, send them to the admin login form
if (!isset($_SESSION['user_id']) && !isset($_SESSION['user']['id'])) {
    header("Location: /Cartify/Admin/login.php");
    exit;
}

$admin_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : (int)$_SESSION['user']['id'];

// 2. LIVE ROLE CHECK: Query database to verify 'admin' status
try {
    $adminCheck = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $adminCheck->execute([$admin_id]);
    $liveRole = $adminCheck->fetchColumn();

    if ($liveRole !== 'admin') {
        // Log them out of the admin session and send to login with error
        unset($_SESSION['admin_logged']);
        header("Location: /Cartify/Admin/login.php");
        exit;
    }
} catch (PDOException $e) {
    header("Location: /Cartify/Admin/login.php");
    exit;
}

$_SESSION['admin_logged'] = true; 

$active_tab = $_GET['tab'] ?? 'orders';
$message = '';
$error = '';

// ----------------------------------------------------
// ACTIONS HANDLER (Status, Promo, Add, Delete)
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Order Status Update
    if (isset($_POST['update_status'])) {
        $order_id = (int)$_POST['order_id'];
        $new_status = htmlspecialchars($_POST['status']);
        $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$new_status, $order_id]);
        header("Location: index.php?tab=orders&success=status");
        exit;
    }

    // Promo Generation
    if (isset($_POST['create_promo'])) {
        $code = strtoupper(trim($_POST['code']));
        $type = htmlspecialchars($_POST['discount_type']);
        $value = (float)$_POST['discount_value'];
        $min_val = (float)$_POST['min_order_value'];
        $expiry = htmlspecialchars($_POST['expiry_date']);
        try {
            $pdo->prepare("INSERT INTO promo_codes (code, discount_type, discount_value, min_order_value, expiry_date, is_active) VALUES (?, ?, ?, ?, ?, 1)")
                ->execute([$code, $type, $value, $min_val, $expiry]);
            header("Location: index.php?tab=promos&success=promo");
            exit;
        } catch (PDOException $e) { $error = "Promo code already exists."; }
    }

    // ADD USER ACTION
    if (isset($_POST['add_user'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $role = $_POST['role']; // 'customer' or 'merchant'

        try {
            $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)")->execute([$name, $email, $password, $role]);
            $new_user_id = $pdo->lastInsertId();

            if ($role === 'merchant') {
                $biz_name = trim($_POST['business_name']);
                if(empty($biz_name)) { $biz_name = $name . " Shop"; }
                
                $pdo->prepare("INSERT INTO merchants (user_id, business_name, business_email) VALUES (?, ?, ?)")->execute([$new_user_id, $biz_name, $email]);
            }
            header("Location: index.php?tab=users&success=added");
            exit;
        } catch (PDOException $e) { $error = "User creation failed: " . $e->getMessage(); }
    }

    // ADD MERCHANT ACTION
    if (isset($_POST['add_merchant'])) {
        $user_id = (int)$_POST['user_id'];
        $biz_name = trim($_POST['business_name']);
        $biz_email = trim($_POST['business_email']);

        try {
            $pdo->prepare("UPDATE users SET role = 'merchant' WHERE id = ?")->execute([$user_id]);
            $pdo->prepare("INSERT INTO merchants (user_id, business_name, business_email) VALUES (?, ?, ?)")->execute([$user_id, $biz_name, $biz_email]);
            header("Location: index.php?tab=merchants&success=m_added");
            exit;
        } catch (PDOException $e) { $error = "Failed to add merchant profile row: " . $e->getMessage(); }
    }

    // DELETE USER / MERCHANT ACTIONS
    if (isset($_POST['delete_target'])) {
        $target_id = (int)$_POST['target_id'];
        $type = $_POST['target_type'];

        if ($type === 'user') {
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$target_id]);
            header("Location: index.php?tab=users&success=deleted");
       } elseif ($type === 'merchant') {
        try {
            $pdo->beginTransaction();

            $user_id = (int)$_POST['target_id']; 

            $stmt1 = $pdo->prepare("DELETE FROM merchants WHERE user_id = ?");
            $stmt1->execute([$user_id]);

            $stmt2 = $pdo->prepare("DELETE FROM products WHERE merchant_id = ?");
            $stmt2->execute([$user_id]);

            $stmt3 = $pdo->prepare("UPDATE users SET role = 'customer', merchant_status = 'revoked' WHERE id = ?");
            $stmt3->execute([$user_id]);

            $pdo->commit();
            header("Location: index.php?tab=merchants&success=m_revoked");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Failed to completely wipe merchant profile and active catalog: " . $e->getMessage();
        }
    }
        exit;
    }
}

// ----------------------------------------------------
// DATA QUERIES
// ----------------------------------------------------
$total_revenue = $pdo->query("SELECT SUM(total_price) FROM orders WHERE status != 'Cancelled'")->fetchColumn() ?? 0;
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn() ?? 0;
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() ?? 0;

if ($active_tab === 'promos') {
    $promos = $pdo->query("SELECT * FROM promo_codes ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} elseif ($active_tab === 'users') {
    $users = $pdo->query("SELECT * FROM users WHERE role != 'admin' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} elseif ($active_tab === 'merchants') {
    try {
        $merchants = $pdo->query("
            SELECT u.id as user_id, u.name as username, u.email as user_email, 
                   IFNULL(m.id, 0) as merchant_profile_id,
                   IFNULL(m.business_name, 'No Business Profile Setup') as business_name, 
                   IFNULL(m.business_email, u.email) as business_email
            FROM users u
            LEFT JOIN merchants m ON u.id = m.user_id 
            WHERE u.role = 'merchant'
            ORDER BY u.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $merchants = [];
        $error = "Merchants display logic error: " . $e->getMessage();
    }

    try {
        $eligible_users = $pdo->query("SELECT id, name as username FROM users WHERE role = 'customer'")->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $eligible_users = [];
        $error = "Dropdown user load error: " . $e->getMessage();
    }
} else {
    $orders = $pdo->query("SELECT o.*, p.title FROM orders o JOIN products p ON o.product_id = p.id ORDER BY o.id DESC")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cartify Core Control Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="brand-logo">Cartify Admin</div>
        <nav class="nav-menu">
            <a href="index.php?tab=orders" class="<?php echo $active_tab === 'orders' ? 'active' : ''; ?>">📦 Manage Orders</a>
            <a href="index.php?tab=users" class="<?php echo $active_tab === 'users' ? 'active' : ''; ?>">👥 System Users</a>
            <a href="index.php?tab=merchants" class="<?php echo $active_tab === 'merchants' ? 'active' : ''; ?>">🏬 Store Merchants</a>
            <a href="index.php?tab=promos" class="<?php echo $active_tab === 'promos' ? 'active' : ''; ?>">🏷 Promo Codes</a>
            <a href="/Cartify/Products/products.php" target="_blank">🌐 View Storefront</a>
        </nav>
    </aside>

    <!-- Content Workspace -->
    <main class="main-content">
        <h1>Dashboard Control Board Summary</h1>

        <!-- High Level Analytics Metrics -->
        <section class="metrics-grid">
            <div class="metric-card">
                <h3>Total Gross Volume</h3>
                <!-- Corrected: Removed * 83 multiplier -->
                <div class="value">₹<?php echo number_format($total_revenue, 2); ?></div>
            </div>
            <div class="metric-card">
                <h3>Incoming Order Volume</h3>
                <div class="value"><?php echo $total_orders; ?></div>
            </div>
            <div class="metric-card">
                <h3>Registered User Base</h3>
                <div class="value"><?php echo $total_users; ?></div>
            </div>
        </section>

        <?php if(!empty($error)): ?>
            <div style="padding: 16px; background: #fce8e6; color: #c5221f; border-radius: 8px; margin-bottom: 24px; font-weight: 600; font-family: sans-serif; border: 1px solid rgba(197, 34, 31, 0.2);">
                ⚠️ System Warning: <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- TAB: ORDERS MANAGEMENT -->
        <?php if ($active_tab === 'orders'): ?>
            <div class="data-board">
                <h2>Customer Order Fulfillment Matrix</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Item Details</th>
                            <th>Total Price</th>
                            <th>Delivery Point</th>
                            <th>Status Control</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $o): ?>
                            <tr>
                                <td><strong>#CF-<?php echo $o['id']; ?></strong></td>
                                <td><span><?php echo htmlspecialchars($o['title']); ?></span><br><small>Qty: <?php echo $o['quantity']; ?></small></td>
                                <!-- Corrected: Display exact INR price without 83x multiplication -->
                                <td><strong>₹<?php echo number_format($o['total_price'], 2); ?></strong></td>
                                <td><strong><?php echo htmlspecialchars($o['customer_name']); ?></strong> (<?php echo htmlspecialchars($o['customer_phone']); ?>)<br><?php echo htmlspecialchars($o['delivery_address']); ?></td>
                                <td>
                                    <form action="index.php?tab=orders" method="POST">
                                        <input type="hidden" name="update_status" value="1">
                                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                        <select name="status" onchange="this.form.submit()" class="status-select select-<?php echo $o['status']; ?>">
                                            <option value="Pending" <?php echo $o['status'] === 'Pending' ? 'selected' : ''; ?>>⏳ Pending</option>
                                            <option value="Shipped" <?php echo $o['status'] === 'Shipped' ? 'selected' : ''; ?>>🚚 Shipped</option>
                                            <option value="Delivered" <?php echo $o['status'] === 'Delivered' ? 'selected' : ''; ?>>✅ Delivered</option>
                                            <option value="Cancelled" <?php echo $o['status'] === 'Cancelled' ? 'selected' : ''; ?>>✕ Cancelled</option>
                                            <option value="Returned" <?php echo $o['status'] === 'Returned' ? 'selected' : ''; ?>>🔄 Returned</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <!-- TAB: USERS SECTION -->
        <?php elseif ($active_tab === 'users'): ?>
            <div class="promo-split-container">
                <div class="data-board form-panel">
                    <h2>Add New Site User</h2>
                    <form action="index.php?tab=users" method="POST">
                        <input type="hidden" name="add_user" value="1">
                        <div class="form-group"><label>Account Username</label><input type="text" name="name" class="form-control" required></div>
                        <div class="form-group"><label>Email Address</label><input type="email" name="email" class="form-control" required></div>
                        <div class="form-group"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                        <div class="form-group">
                            <label>Default Role Matrix</label>
                            <select name="role" class="form-control" id="userRoleSelect" onchange="toggleBizField(this.value)">
                                <option value="customer">Standard Customer User</option>
                                <option value="merchant">Dual Customer + Merchant User</option>
                            </select>
                        </div>
                        <div class="form-group" id="bizField" style="display:none;"><label>Business Brand Name</label><input type="text" name="business_name" class="form-control"></div>
                        <button type="submit" class="submit-btn">Create User Account</button>
                    </form>
                </div>
                <div class="data-board table-panel">
                    <h2>Registered User Index</h2>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User Profile Details</th>
                                <th>Role Tier</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($users as $u): ?>
                                <tr>
                                    <td>#U-<?php echo $u['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($u['name']); ?></strong><br><small><?php echo htmlspecialchars($u['email']); ?></small></td>
                                    <td>
                                        <?php if($u['role'] === 'merchant'): ?>
                                            <span class="role-badge badge-merchant">🏬 Customer & Merchant</span>
                                        <?php else: ?>
                                            <span class="role-badge badge-customer">👤 Customer</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form action="index.php?tab=users" method="POST" onsubmit="return confirm('Completely remove this user registration account permanently?');">
                                            <input type="hidden" name="delete_target" value="1">
                                            <input type="hidden" name="target_type" value="user">
                                            <input type="hidden" name="target_id" value="<?php echo $u['id']; ?>">
                                            <button type="submit" class="delete-btn-action">Remove Account</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

       <!-- TAB: MERCHANTS SECTION -->
        <?php elseif ($active_tab === 'merchants'): ?>
            <div class="promo-split-container">
                <div class="data-board form-panel">
                    <h2>Register Existing User as Merchant</h2>
                    <form action="index.php?tab=merchants" method="POST">
                        <input type="hidden" name="add_merchant" value="1">
                        <div class="form-group">
                            <label>Select Eligible Customer Profile</label>
                            <select name="user_id" class="form-control" required>
                                <option value="">-- Choose User Account --</option>
                                <?php if (!empty($eligible_users)): ?>
                                    <?php foreach($eligible_users as $eu): ?>
                                        <option value="<?php echo $eu['id']; ?>"><?php echo htmlspecialchars($eu['username']); ?> (#U-<?php echo $eu['id']; ?>)</option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No customer accounts available to promote</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group"><label>Business Brand Name</label><input type="text" name="business_name" class="form-control" required></div>
                        <div class="form-group"><label>Business Contact Email</label><input type="email" name="business_email" class="form-control" required></div>
                        <button type="submit" class="submit-btn">Grant Merchant Authorization</button>
                    </form>
                </div>

                <div class="data-board table-panel">
                    <h2>Authorized Store Merchants</h2>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Merchant Profile</th>
                                <th>Connected User Account</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($merchants)): ?>
                                <?php foreach($merchants as $m): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($m['business_name']); ?></strong><br><small>Biz Email: <?php echo htmlspecialchars($m['business_email']); ?></small></td>
                                        <td><?php echo htmlspecialchars($m['username']); ?><br><small>User Email: <?php echo htmlspecialchars($m['user_email']); ?></small></td>
                                        <td>
                                            <form action="index.php?tab=merchants" method="POST" onsubmit="return confirm('Revoke merchant rights? User account defaults back to customer layer.');">
                                                <input type="hidden" name="delete_target" value="1">
                                                <input type="hidden" name="target_type" value="merchant">
                                                <input type="hidden" name="target_id" value="<?php echo $m['user_id']; ?>">
                                                <button type="submit" class="delete-btn-action">Revoke Merchant Status</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align: center; color: #777; padding: 30px;">
                                        No authorized merchants found.<br>
                                        <small>Create one using the form on the left!</small>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <!-- TAB: PROMO CODE CONFIGURATION -->
        <?php elseif ($active_tab === 'promos'): ?>
            <div class="promo-split-container">
                <div class="data-board form-panel">
                    <h2>Generate Dynamic Promo Coupon</h2>
                    <form action="index.php?tab=promos" method="POST">
                        <input type="hidden" name="create_promo" value="1">
                        <div class="form-group"><label>Coupon Code String</label><input type="text" name="code" class="form-control" placeholder="E.G. FESTIVE50" style="text-transform: uppercase;" onkeyup="this.value = this.value.toUpperCase();" required></div>
                        <div class="form-group">
                            <label>Calculation Type</label>
                            <select name="discount_type" class="form-control">
                                <option value="percentage">Percentage Discount (%)</option>
                                <option value="flat">Flat Currency Deduction (₹)</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Value Magnitude</label><input type="number" step="0.01" name="discount_value" class="form-control" required></div>
                        <div class="form-group"><label>Minimum Required Basket Spend (₹)</label><input type="number" step="0.01" name="min_order_value" class="form-control" value="0.00"></div>
                        <div class="form-group"><label>Expiration Date</label><input type="date" name="expiry_date" class="form-control" required></div>
                        <button type="submit" class="submit-btn">Inject Active Code</button>
                    </form>
                </div>
                <div class="data-board table-panel">
                    <h2>Configured Promotional Coupons</h2>
                    <table class="admin-table">
                        <thead><tr><th>Code</th><th>Scale</th><th>Min Spend</th><th>Expiry</th></tr></thead>
                        <tbody>
                            <?php foreach($promos as $p): ?>
                                <tr>
                                    <td><span class="promo-pill-badge"><?php echo htmlspecialchars($p['code']); ?></span></td>
                                    <td><strong><?php echo (strtolower($p['discount_type']) === 'percentage' || strtolower($p['discount_type']) === 'percent') ? ((int)$p['discount_value'] . '%') : ('₹' . number_format($p['discount_value'], 2)); ?> OFF</strong></td>
                                    <td>₹<?php echo number_format($p['min_order_value'], 2); ?></td>
                                    <td><small><?php echo date('M d, Y', strtotime($p['expiry_date'])); ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script>
        function toggleBizField(val) {
            document.getElementById('bizField').style.display = (val === 'merchant') ? 'block' : 'none';
        }
    </script>
</body>
</html>
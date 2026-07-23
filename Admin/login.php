<?php
// Admin/login.php
require_once '../db.php';
session_start();

// If already logged in as admin, redirect directly to dashboard
if (isset($_SESSION['user_id'])) {
    $check = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $check->execute([(int)$_SESSION['user_id']]);
    if ($check->fetchColumn() === 'admin') {
        header("Location: index.php");
        exit;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        try {
            // Fetch user by email
            $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password AND ensure role is strictly 'admin'
            if ($user && password_verify($password, $user['password'])) {
                if ($user['role'] === 'admin') {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = 'admin';
                    $_SESSION['admin_logged'] = true;

                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Access denied: This account does not have administrator privileges.";
                }
            } else {
                $error = "Invalid administrator credentials.";
            }
        } catch (PDOException $e) {
            $error = "Database connection error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartify - Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: #f7f7f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 440px;
            padding: 48px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
            text-align: center;
            border: 1px solid #eaeaea;
        }

        .brand-logo {
            font-size: 2.2rem;
            font-weight: 700;
            color: #556246; /* Matching Cartify brand olive */
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            font-size: 0.75rem;
            font-weight: 700;
            color: #8c8c8c;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 36px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 22px;
        }

        label {
            display: block;
            font-size: 0.88rem;
            font-weight: 600;
            color: #333333;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            font-size: 0.95rem;
            border: 1.5px solid #e1e1e1;
            border-radius: 10px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            color: #1a1a1a;
            background-color: #ffffff;
        }

        .form-control:focus {
            border-color: #111111;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
        }

        .form-control::placeholder {
            color: #a0a0a0;
        }

        .alert-error {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 20px;
            text-align: left;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background-color: #111111;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .submit-btn:hover {
            background-color: #222222;
        }

        .submit-btn:active {
            transform: scale(0.99);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand-logo">Cartify</div>
        <div class="subtitle">ADMIN CONTROL PANEL</div>

        <?php if (!empty($error)): ?>
            <div class="alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Admin Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="admin@cartify.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="password">Master Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="submit-btn">Authorize & Enter</button>
        </form>
    </div>

</body>
</html>
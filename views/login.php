<?php
if (session_status() == PHP_SESSION_NONE) {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
}

$totalAmount = 0;
if (isset($_SESSION['sales']) && is_array($_SESSION['sales'])) {
    foreach ($_SESSION['sales'] as $item) {
        $totalAmount += $item['sell_price'] * $item['quantity'];
    }
}


// Store the totalAmount in the session for easy access later
$_SESSION['totalAmount'] = $totalAmount;


if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add an item to the cart (example)
if (isset($_POST['add_to_cart'])) {
    $productCode = $_POST['product_code'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];  // Assuming price is sent in the form

    // Add item to the cart or update quantity if the product is already in the cart
    if (isset($_SESSION['cart'][$productCode])) {
        $_SESSION['cart'][$productCode]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$productCode] = [
            'quantity' => $quantity,
            'price' => $price
        ];
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Infinite POS</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: url('/images/login.jpg') no-repeat center center/cover;
}


        .login-container {
            display: flex;
            width: 800px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Left Section - Logo & Greeting */
        .login-left {
            width: 50%;
            background: #88976C; /* Dark gray/black */
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            text-align: center;
        }

        .login-left img {
            width: 150px;
            margin-bottom: 20px;
        }

        .login-left h2 {
            font-size: 22px;
            font-weight: bold;
        }

        /* Right Section - Login Form */
        .login-right {
            width: 50%;
            padding: 40px;
            text-align: center;
        }

        .login-right h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group label {
            font-size: 14px;
            font-weight: bold;
            color: #444;
        }

        .input-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            background: #f9f9f9;
        }

        .input-group input:focus {
            border-color: #222;
            outline: none;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: #88976C; /* Black/Gray */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .login-btn:hover {
            background: #444; /* Slightly lighter gray */
        }

        .error {
            color: #D9534F;
            margin-bottom: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Section -->
        <div class="login-left">
            <img src="/images/logo.png" alt="Infinite POS Logo">
            <h2>Welcome to Infinite POS!</h2>
        </div>

<!-- Right Section -->
<div class="login-right">
    <h2>Login</h2>

    <?php 
    // Generic login errors (only if not locked)
    if (!empty($_SESSION['login_error']) && empty($_SESSION['lock_remaining'])): 
        echo '<p class="error">'.htmlspecialchars($_SESSION['login_error']).'</p>';
        unset($_SESSION['login_error']);
    endif; 
    ?>

    <?php if (!empty($_SESSION['lock_remaining'])): ?>
        <!-- Lock message -->
        <p class="error">
            Account locked. Try again in <span id="countdown"><?php echo (int)$_SESSION['lock_remaining']; ?></span> seconds.
        </p>

        <!-- Live countdown JS -->
        <script>
            let remaining = parseInt(document.getElementById('countdown').textContent, 10);
            const interval = setInterval(() => {
                remaining--;
                if (remaining <= 0) {
                    clearInterval(interval);
                    document.getElementById('countdown').textContent = "0";
                    location.reload();
                } else {
                    document.getElementById('countdown').textContent = remaining;
                }
            }, 1000);
        </script>
        <?php unset($_SESSION['lock_remaining']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <p class="message" style="color: #28a745;"><?php 
            echo htmlspecialchars($_SESSION['success']); 
            unset($_SESSION['success']); 
        ?></p>
    <?php endif; ?>


            <form action="/user/do_login" method="post">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
<div class="input-group" style="position: relative;">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" placeholder="Enter your password" required>
    <!-- Toggle button -->
    <button type="button" onclick="togglePassword()" 
            style="position: absolute; right: 10px; top: 35px; border: none; background: none; cursor: pointer; font-size: 16px;">
        👁
    </button>
</div>
                <button type="submit" class="login-btn">Login</button>

                <!-- DEMO BUTTON -->
                <button type="button" onclick="fillDemo()" class="login-btn"
                    style="background:#4a7c3f;margin-top:10px;">
                    Try Demo &#8212; Auto Fill &amp; Login
                </button>

                <!-- DEMO CREDENTIALS CARD -->
                <div style="margin-top:14px;padding:12px 14px;background:#f4f7f0;border:1px solid #c5d4b0;border-radius:6px;text-align:left;font-size:13px;color:#444;">
                    <div style="font-weight:bold;color:#88976C;margin-bottom:6px;">Demo Credentials</div>
                    <div>Username: <strong>demo</strong></div>
                    <div>Password: <strong>Demo123!</strong></div>
                    <div>Secret Key: <strong>112345</strong></div>
                </div>

                <a href="/user/register" style="display: block; margin-top: 15px; text-decoration: none;">
                    <button type="button" class="login-btn" style="background: #6c757d;">Register</button>
                </a>
<br>
                    <div class="forgot-password">
                    <a href="/user/forgotpass">Forgot your password?</a>
                </div>
            </form>
        </div>
    </div>

<script>
function togglePassword() {
    const pwdInput = document.getElementById('password');
    if (pwdInput.type === 'password') {
        pwdInput.type = 'text';
    } else {
        pwdInput.type = 'password';
    }
}

function fillDemo() {
    // Fill username and password
    document.getElementById('username').value = 'demo';
    document.getElementById('password').value = 'Demo123!';

    // Fill secret key if the field exists
    const secretKey = document.getElementById('secret_key') || document.getElementById('admin_key') || document.querySelector('input[name="secret_key"]') || document.querySelector('input[name="admin_key"]') || document.querySelector('input[name="key"]');
    if (secretKey) {
        secretKey.value = '112345';
    }

    // Submit the form
    document.querySelector('form').submit();
}
</script>    
</body>
</html>
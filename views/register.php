<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = $_SESSION['register_error'] ?? '';
unset($_SESSION['register_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Infinite POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            background: url('/POSu/images/login.jpg') no-repeat center center/cover; 
            font-family: Arial, sans-serif; 
        }
        .register-container { 
            display: flex; 
            width: 800px; 
            background: white; 
            border-radius: 10px; 
            overflow: hidden; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
        }
        .register-left { 
            width: 50%; 
            background: #88976C; 
            color: white; 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            padding: 40px; 
            text-align: center; 
        }
        .register-left img { width: 150px; margin-bottom: 20px; }
        .register-left h2 { font-size: 22px; font-weight: bold; }
        .register-right { width: 50%; padding: 40px; text-align: center; }
        .register-right h2 { color: #333; margin-bottom: 20px; font-size: 24px; font-weight: bold; }
        .input-group { margin-bottom: 15px; text-align: left; }
        .input-group label { font-size: 14px; font-weight: bold; color: #444; }
        .input-group input, .input-group select { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
            font-size: 16px; 
            background: #f9f9f9; 
        }
        .register-btn { 
            width: 100%; 
            padding: 12px; 
            background: #88976C; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: bold; 
            transition: background 0.3s ease; 
        }
        .register-btn:hover { background: #444; }
        .message { margin-bottom: 10px; font-size: 14px; }
        .error { color: #D9534F; }
    </style>
</head>
<body>

    <div class="register-container">

        <div class="register-left">
            <img src="/POSu/images/logo.png" alt="Infinite POS Logo">
            <h2>Join Infinite POS Today!</h2>
        </div>

        <div class="register-right">
            <h2>Register</h2>

            <?php if (!empty($error)): ?>
                <p class="message error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

<form id="registerForm" action="/POSu/index.php" method="post">
    <input type="hidden" name="action" value="do_register">

    <div class="input-group">
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" required>
    </div>

    <div class="input-group">
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" required>
    </div>

    <div class="input-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" onkeyup="checkUsername()" required>
        <span id="user-msg" style="font-size:13px; font-weight:bold; display:block; margin-top:5px;"></span>
    </div>

    <div class="input-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <div style="font-size: 13px; color: #555; margin-top: 4px;">
            Password must contain:
            <ul style="margin: 5px 0 0 18px; padding: 0; font-size: 12px; color:#555;">
                <li>At least 8 characters</li>
                <li>Uppercase letter (A–Z)</li>
                <li>Lowercase letter (a–z)</li>
                <li>Number (0–9)</li>
                <li>Symbol (e.g. ! @ # $ % & *)</li>
            </ul>
        </div>
        <span id="password_msg" style="font-size:13px;font-weight:bold;"></span>
    </div>

    <div class="input-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <span id="confirm_msg" style="font-size:13px;font-weight:bold;"></span>
    </div>

    <div class="input-group">
        <label for="role">Role</label>
        <select id="role" name="role" required>
            <option value="">Select a role</option>
            <option value="admin">Admin</option>
            <option value="cashier">Cashier</option>
        </select>
    </div>

    <div class="input-group" id="admin_key_group" style="display:none;">
        <label for="admin_key">Admin Secret Key</label>
        <input type="password" id="admin_key" name="admin_key" placeholder="Enter admin key">
        <span id="admin_key_msg" style="font-size:13px;font-weight:bold;"></span>
    </div>

    <button type="submit" class="register-btn">Register</button>
</form>

<script>
const form = document.getElementById('registerForm');
const passwordInput = document.getElementById('password');
const confirmInput = document.getElementById('confirm_password');
const usernameMsg = document.getElementById('user-msg');
const adminKeyInput = document.getElementById('admin_key');
const adminKeyMsg = document.getElementById('admin_key_msg');

form.addEventListener('submit', function(e) {
    let valid = true;

    // Password validation
    const strong = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
    if (!strong.test(passwordInput.value)) {
        valid = false;
        password_msg.textContent = "❌ Password must meet requirements.";
        password_msg.style.color = "red";
    }

    // Confirm password match
    if (passwordInput.value !== confirmInput.value) {
        valid = false;
        confirm_msg.textContent = "❌ Passwords do not match.";
        confirm_msg.style.color = "red";
    } else {
        confirm_msg.textContent = "";
    }

    // Username availability
    if (usernameMsg.textContent.includes('❌')) {
        valid = false;
        alert('Username already taken.');
    }

    // Admin key check
    if (document.getElementById('role').value === 'admin' && adminKeyInput.value.trim() === '') {
        valid = false;
        adminKeyMsg.textContent = "❌ Admin key required.";
        adminKeyMsg.style.color = "red";
    } else {
        adminKeyMsg.textContent = "";
    }

    if (!valid) e.preventDefault();
});
</script>
<script>
function checkUsername() {
    let username = document.getElementById("username").value;
    const msg = document.getElementById("user-msg");

    if (username.trim() === "") {
        msg.textContent = "";
        return;
    }

    fetch(`/POSu/index.php?url=check_username&username=` + encodeURIComponent(username))
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                msg.textContent = "❌ Username already taken";
                msg.style.color = "red";
            } else {
                msg.textContent = "✔ Username available";
                msg.style.color = "green";
            }
        })
        .catch(err => {
            console.error(err);
            msg.textContent = "";
        });
}

</script>

<script>
document.getElementById("role").addEventListener("change", function() {
    const adminKeyGroup = document.getElementById("admin_key_group");
    adminKeyGroup.style.display = (this.value === "admin") ? "block" : "none";
});
</script>
<script>
document.getElementById("password").addEventListener("input", validatePassword);

function validatePassword() {
    const pwd = document.getElementById("password").value;
    const msg = document.getElementById("password_msg");

    // Regex: strong password
    const strong = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    if (!strong.test(pwd)) {
        msg.textContent = "❌ Password must be at least 8 characters with uppercase, lowercase, number, and symbol.";
        msg.style.color = "red";
    } else {
        msg.textContent = "✔ Strong password";
        msg.style.color = "green";
    }
}
</script>

</body>
</html>

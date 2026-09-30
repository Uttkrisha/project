<?php
require_once 'config.php';
if (isLoggedIn()) redirect('index.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $error = 'Please fill in all fields';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'Username or email already exists';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'customer')");
            $stmt->bind_param("sss", $username, $email, $hashed);
            if ($stmt->execute()) {
                $success = 'Welcome to Glow Skin! You can now sign in.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up — K-beauty</title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='40' fill='%23c4ff4d'/></svg>">
<style>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,700&family=Manrope:wght@300;400;500;600;700;800&display=swap');

:root{
  --ink:#0e0e0e; --ink-soft:#2a2a2a; --smoke:#6b6b6b; --mist:#a8a8a8;
  --cloud:#e8e6e1; --paper:#f5f3ef; --paper-light:#fbfaf8; --white:#fff;
  --accent:#c4ff4d; --accent-dark:#a8e035; --coral:#ff7a5c; --coral-dark:#e85a3a;
  --radius-sm:8px; --radius:20px; --radius-lg:32px; --radius-full:999px;
  --ease:cubic-bezier(.22,1,.36,1); --transition:all .5s var(--ease);
}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Manrope',system-ui,sans-serif;background:#eed8e5;color:var(--ink);line-height:1.5;-webkit-font-smoothing:antialiased;min-height:100vh;display:flex;flex-direction:column}
h1,h2,h3{font-family:'Fraunces',Georgia,serif;font-weight:500;letter-spacing:-.03em;line-height:1.05}

.navbar{position:sticky;top:0;z-index:1000;background:rgba(245,243,239,.85);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-bottom:1px solid rgba(14,14,14,.06)}
.nav-container{max-width:1440px;margin:0 auto;padding:.8rem 2.5rem;display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap}
.logo{font-family:'Fraunces',serif;font-size:1.5rem;font-weight:700;color:var(--ink);text-decoration:none;letter-spacing:-.04em;display:flex;align-items:center;gap:.4rem}
.logo::before{content:'';width:8px;height:8px;background:#E8B4C0;border-radius:50%;display:inline-block}
.nav-links{display:flex;gap:.25rem;align-items:center;flex-wrap:wrap}
.nav-links a{color:var(--ink);text-decoration:none;font-weight:500;font-size:.875rem;padding:.6rem 1.1rem;border-radius:var(--radius-full);transition:var(--transition)}
.nav-links a:hover{background:var(--ink);color:var(--paper)}
.nav-links a:last-child{background:var(--ink);color:var(--paper);padding:.65rem 1.4rem}
.nav-links a:last-child:hover{background:var(--accent);color:var(--ink)}

.container{max-width:1440px;margin:0 auto;padding:2rem 2.5rem 5rem;flex:1;width:100%}

.form-wrap{display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center;max-width:1100px;margin:3rem auto 0}

.form-side{padding:1rem}
.form-side h2{font-size:clamp(2rem,4vw,3rem);font-weight:400;margin-bottom:.75rem;color:var(--ink);letter-spacing:-.045em;line-height:1}
.form-side h2 em{font-style:italic;color:var(--smoke);font-weight:300}
.form-subtitle{color:var(--smoke);margin-bottom:2.5rem;font-size:1rem;font-weight:400;max-width:420px}
.form-eyebrow{display:inline-block;font-size:.7rem;font-weight:700;letter-spacing:.2em;text-transform:uppercase;color:var(--smoke);margin-bottom:1rem}

.form-perks{list-style:none;margin-top:2rem;display:flex;flex-direction:column;gap:.9rem}
.form-perks li{display:flex;align-items:center;gap:.75rem;font-size:.9rem;color:var(--ink-soft);font-weight:500}
.form-perks li::before{content:'✓';display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;background:var(--accent);color:var(--ink);border-radius:50%;font-size:.7rem;font-weight:800;flex-shrink:0}

.form-card{background:var(--white);padding:3rem 2.75rem;border-radius:var(--radius-lg);box-shadow:0 20px 60px -30px rgba(14,14,14,.15);animation:fadeUp .6s var(--ease)}
@keyframes fadeUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}

.form-group{margin-bottom:1.4rem}
.form-group label{display:block;margin-bottom:.5rem;font-weight:600;color:var(--ink);font-size:.75rem;letter-spacing:.08em;text-transform:uppercase}
.form-group input{width:100%;padding:.95rem 1rem;border:1.5px solid var(--cloud);border-radius:var(--radius-sm);font-size:.95rem;font-family:inherit;background:var(--paper-light);transition:var(--transition);color:var(--ink);font-weight:500}
.form-group input:focus{outline:none;border-color:var(--ink);background:var(--white);box-shadow:0 0 0 4px rgba(196,255,77,.25)}
.form-group input::placeholder{color:var(--mist);font-weight:400}

.form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}

.btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:.95rem 1.5rem;border:none;border-radius:var(--radius-sm);font-size:.9rem;font-weight:600;font-family:inherit;cursor:pointer;text-decoration:none;transition:var(--transition);white-space:nowrap;width:100%}
.btn-primary{background:var(--ink);color:var(--paper)}
.btn-primary:hover{background:var(--accent);color:var(--ink);transform:translateY(-2px)}

.alert{padding:1rem 1.25rem;border-radius:var(--radius-sm);margin-bottom:1.5rem;font-weight:500;font-size:.875rem;display:flex;align-items:center;gap:.75rem;animation:fadeUp .4s var(--ease)}
.alert::before{content:'';width:8px;height:8px;border-radius:50%;flex-shrink:0}
.alert-error{background:#ffe4dc;color:var(--coral-dark)}
.alert-error::before{background:var(--coral-dark)}
.alert-success{background:var(--accent);color:var(--ink)}
.alert-success::before{background:var(--ink)}

.form-footer{text-align:center;margin-top:1.75rem;color:var(--smoke);font-size:.875rem}
.form-footer a{color:var(--ink);font-weight:600;text-decoration:none;border-bottom:1.5px solid var(--accent);transition:var(--transition)}
.form-footer a:hover{background:var(--accent)}

@media (max-width:900px){
  .form-wrap{grid-template-columns:1fr;gap:2rem;margin-top:1.5rem}
  .form-side{text-align:center}
  .form-side .form-perks{max-width:320px;margin-left:auto;margin-right:auto;text-align:left}
  .container{padding:1.5rem 1.25rem 3rem}
  .form-card{padding:2rem 1.5rem}
  .form-row{grid-template-columns:1fr}
}
</style>
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo">K-beauty</a>
        <div class="nav-links">
            <a href="index.php">Shop</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
            <a href="login.php">Login</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="form-wrap">
        <div class="form-side">
            <span class="form-eyebrow">✦ Join the ritual</span>
            <h2>Begin your<br><em>glow</em> journey.</h2>
            <p class="form-subtitle">Create an account to unlock personalized skincare, save your orders, and get first access to new launches.</p>
            <ul class="form-perks">
                <li>Track all your orders in one place</li>
                <li>Faster checkout on every purchase</li>
                <li>Early access to new collections</li>
            </ul>
        </div>

        <div class="form-card">
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="Choose a username" autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="you@example.com" autocomplete="email">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Min 6 chars" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm</label>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Repeat" autocomplete="new-password">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Create Account →</button>
            </form>
            <p class="form-footer">Already have an account? <a href="login.php">Sign in</a></p>
        </div>
    </div>
</div>
</body>
</html>
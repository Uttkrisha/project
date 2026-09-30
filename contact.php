<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email';
    } elseif (strlen($message) < 10) {
        $error = 'Message must be at least 10 characters';
    } else {
        $success = 'Thank you, ' . htmlspecialchars($name) . '! We\'ll get back to you within 24 hours.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact — K-beauty </title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='40' fill='%23c4ff4d'/></svg>">
<style>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,500;9..144,700&family=Manrope:wght@300;400;500;600;700;800&display=swap');
:root{--ink:#0e0e0e;--ink-soft:#2a2a2a;--smoke:#6b6b6b;--mist:#a8a8a8;--cloud:#e8e6e1;--paper:#eed8e5;--paper-light:#fbfaf8;--white:#fff;--accent:#c4ff4d;--accent-dark:#a8e035;--coral:#ff7a5c;--coral-dark:#e85a3a;--lavender:#d4c5f9;--radius-sm:8px;--radius:20px;--radius-lg:32px;--radius-full:999px;--ease:cubic-bezier(.22,1,.36,1);--transition:all .5s var(--ease)}
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
body{font-family:'Manrope',system-ui,sans-serif;background:var(--paper);color:var(--ink);line-height:1.5;-webkit-font-smoothing:antialiased;overflow-x:hidden}
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
.container{max-width:1440px;margin:0 auto;padding:0 2.5rem 5rem}

.contact-hero{padding:5rem 2.5rem 3rem;max-width:1440px;margin:0 auto;text-align:center;animation:fadeIn 1s var(--ease) both}
.eyebrow{display:inline-block;font-size:.7rem;font-weight:700;letter-spacing:.2em;text-transform:uppercase;color:var(--smoke);margin-bottom:1rem}
.contact-hero h1{font-size:clamp(2.5rem,6vw,5rem);font-weight:400;color:var(--ink);letter-spacing:-.05em;line-height:.95;margin-bottom:1.5rem}
.contact-hero h1 em{font-style:italic;font-weight:300;color:var(--smoke)}
.contact-hero p{font-size:1.1rem;color:var(--smoke);max-width:560px;margin:0 auto;line-height:1.7}

.contact-grid{display:grid;grid-template-columns:1fr 1.3fr;gap:3rem;max-width:1100px;margin:4rem auto 0}

.contact-info{display:flex;flex-direction:column;gap:1.5rem}
.info-card{background:var(--white);border-radius:var(--radius);padding:1.75rem;transition:var(--transition);border:1px solid transparent}
.info-card:hover{transform:translateY(-3px);border-color:var(--ink)}
.info-icon{width:44px;height:44px;background:var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem;margin-bottom:1rem}
.info-card h3{font-size:1.05rem;font-weight:500;margin-bottom:.35rem;color:var(--ink)}
.info-card p{color:var(--smoke);font-size:.9rem;line-height:1.6}
.info-card a{color:var(--ink);text-decoration:none;font-weight:600;border-bottom:1.5px solid var(--accent);transition:var(--transition)}
.info-card a:hover{background:var(--accent)}

.contact-form-card{background:var(--white);border-radius:var(--radius-lg);padding:2.75rem;box-shadow:0 20px 60px -30px rgba(14,14,14,.15);animation:fadeUp .6s var(--ease)}
.contact-form-card h2{font-size:1.75rem;font-weight:400;color:var(--ink);letter-spacing:-.04em;margin-bottom:.5rem}
.contact-form-card .sub{color:var(--smoke);font-size:.9rem;margin-bottom:2rem}

.form-group{margin-bottom:1.4rem}
.form-group label{display:block;margin-bottom:.5rem;font-weight:600;color:var(--ink);font-size:.75rem;letter-spacing:.08em;text-transform:uppercase}
.form-group input,.form-group textarea{width:100%;padding:.95rem 1rem;border:1.5px solid var(--cloud);border-radius:var(--radius-sm);font-size:.95rem;font-family:inherit;background:var(--paper-light);transition:var(--transition);color:var(--ink);font-weight:500;resize:vertical}
.form-group input:focus,.form-group textarea:focus{outline:none;border-color:var(--ink);background:var(--white);box-shadow:0 0 0 4px rgba(196,255,77,.25)}
.form-group input::placeholder,.form-group textarea::placeholder{color:var(--mist);font-weight:400}

.btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:.95rem 1.5rem;border:none;border-radius:var(--radius-sm);font-size:.9rem;font-weight:600;font-family:inherit;cursor:pointer;text-decoration:none;transition:var(--transition);width:100%}
.btn-primary{background:var(--ink);color:var(--paper)}
.btn-primary:hover{background:var(--accent);color:var(--ink);transform:translateY(-2px)}

.alert{padding:1rem 1.25rem;border-radius:var(--radius-sm);margin-bottom:1.5rem;font-weight:500;font-size:.875rem;display:flex;align-items:center;gap:.75rem;animation:fadeUp .4s var(--ease)}
.alert::before{content:'';width:8px;height:8px;border-radius:50%;flex-shrink:0}
.alert-error{background:#ffe4dc;color:var(--coral-dark)}
.alert-error::before{background:var(--coral-dark)}
.alert-success{background:var(--accent);color:var(--ink)}
.alert-success::before{background:var(--ink)}

.faq{max-width:900px;margin:5rem auto 0}
.faq h2{font-size:clamp(1.75rem,3vw,2.5rem);font-weight:400;color:var(--ink);letter-spacing:-.04em;text-align:center;margin-bottom:2.5rem}
.faq h2 em{font-style:italic;color:var(--smoke);font-weight:300}
.faq-item{background:var(--white);border-radius:var(--radius);padding:1.5rem 1.75rem;margin-bottom:.75rem;transition:var(--transition);border:1px solid transparent}
.faq-item:hover{border-color:var(--ink)}
.faq-item h3{font-family:'Manrope',sans-serif;font-size:1rem;font-weight:600;color:var(--ink);margin-bottom:.4rem;letter-spacing:0}
.faq-item p{color:var(--smoke);font-size:.9rem;line-height:1.6}

.footer{background:var(--ink);color:var(--paper);padding:4rem 2.5rem 2rem;margin-top:5rem}
.footer-inner{max-width:1440px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;gap:2rem;flex-wrap:wrap}
.footer-brand{font-family:'Fraunces',serif;font-size:1.75rem;font-weight:700;color:var(--paper);letter-spacing:-.04em}
.footer-brand::before{content:'';display:inline-block;width:8px;height:8px;background:var(--accent);border-radius:50%;margin-right:.5rem;vertical-align:middle}
.footer p{color:var(--mist);font-size:.85rem}

@keyframes fadeIn{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
@keyframes fadeUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}
@media (max-width:900px){
  .contact-grid{grid-template-columns:1fr;gap:2rem}
  .contact-hero{padding:3rem 1.25rem 2rem}
}
@media (max-width:768px){
  .nav-container{flex-direction:column;gap:1rem;padding:1rem 1.5rem}
  .nav-links{justify-content:center}
  .container{padding:0 1.25rem 3rem}
  .contact-form-card{padding:2rem 1.5rem}
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
            <?php if (isLoggedIn()): ?>
                <?php if (isAdmin()): ?><a href="admin.php">Admin</a><?php endif; ?>
                <a href="cart.php">Cart<?php echo isset($_SESSION['cart']) && count($_SESSION['cart']) > 0 ? ' · '.array_sum($_SESSION['cart']) : ''; ?></a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<section class="contact-hero">
    <span class="eyebrow">✦ Get in Touch</span>
    <h1>We'd love to<br><em>hear from you.</em></h1>
    <p>Questions about a product? Need help with your order? Our team replies within 24 hours, every day.</p>
</section>

<div class="container">
    <div class="contact-grid">
        <div class="contact-info">
            <div class="info-card">
                <div class="info-icon">✉️</div>
                <h3>Email Us</h3>
                <p>For general questions and support<br><a href="mailto:hello@kbeauty.com">hello@kbeauty.com</a></p>
            </div>
            <div class="info-card">
                <div class="info-icon">📞</div>
                <h3>Call Us</h3>
                <p>Mon–Fri, 9am–6pm EST<br><a href="tel:+977-9843256781">+977-9843256781</a></p>
            </div>
            <div class="info-card">
                <div class="info-icon">📍</div>
                <h3>Visit Us</h3>
                <p>K-beauty Studio<br>123 Jhamsikhel, Lalitpur, 10001</p>
            </div>
        </div>

        <div class="contact-form-card">
            <h2>Send a Message</h2>
            <p class="sub">Fill in the form and we'll get back to you shortly.</p>

            <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

            <form method="POST" action="contact.php">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" required placeholder="Ritika Shrestha">
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="you@gmail.com">
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required placeholder="How can we help you?"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message →</button>
            </form>
        </div>
    </div>

    <div class="faq">
        <h2>Frequently <em>asked</em> questions</h2>
        <div class="faq-item">
            <h3>How long does shipping take?</h3>
            <p>Standard shipping is 3–5 business days. Express options are available at checkout.</p>
        </div>
        <div class="faq-item">
            <h3>Do you offer returns?</h3>
            <p>Yes — we accept returns within 30 days of delivery for a full refund, no questions asked.</p>
        </div>
        <div class="faq-item">
            <h3>Are your products vegan?</h3>
            <p>Most are 100% vegan. Product pages list vegan and cruelty-free status for each item.</p>
        </div>
        <div class="faq-item">
            <h3>How do I track my order?</h3>
            <p>Log in and visit your Orders page to see live status for every order you've placed.</p>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="footer-inner">
        <div class="footer-brand">K-beauty</div>
        <p>© 2026 Glow Skin · Naturally radiant skincare for every ritual</p>
    </div>
</footer>
</body>
</html>
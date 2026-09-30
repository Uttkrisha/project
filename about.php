<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About — K-beauty </title>
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

.about-hero{padding:5rem 2.5rem 3rem;max-width:1440px;margin:0 auto;text-align:center;animation:fadeIn 1s var(--ease) both}
.eyebrow{display:inline-block;font-size:.7rem;font-weight:700;letter-spacing:.2em;text-transform:uppercase;color:var(--smoke);margin-bottom:1rem}
.about-hero h1{font-size:clamp(2.5rem,6vw,5rem);font-weight:400;color:var(--ink);letter-spacing:-.05em;line-height:.95;margin-bottom:1.5rem}
.about-hero h1 em{font-style:italic;font-weight:300;color:var(--smoke)}
.about-hero h1 span{background:var(--accent);padding:0 .3em;border-radius:.2em;display:inline-block;transform:rotate(-1deg)}
.about-hero p{font-size:1.1rem;color:var(--smoke);max-width:640px;margin:0 auto;line-height:1.7}

.story-grid{display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center;margin:5rem 0}
.story-visual{aspect-ratio:1;border-radius:var(--radius-lg);background:linear-gradient(135deg,var(--lavender) 0%,var(--coral) 100%);position:relative;overflow:hidden}
.story-visual::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 30% 30%,rgba(255,255,255,.6) 0,transparent 50%),radial-gradient(circle at 70% 70%,rgba(196,255,77,.5) 0,transparent 50%)}
.story-visual-text{position:absolute;bottom:2rem;left:2rem;right:2rem;color:var(--white);font-family:'Fraunces',serif;font-size:1.5rem;font-weight:400;line-height:1.2;letter-spacing:-.03em;text-shadow:0 2px 20px rgba(0,0,0,.15)}
.story-content h2{font-size:clamp(1.75rem,3vw,2.5rem);font-weight:400;color:var(--ink);letter-spacing:-.04em;line-height:1.1;margin-bottom:1.5rem}
.story-content h2 em{font-style:italic;color:var(--smoke);font-weight:300}
.story-content p{color:var(--smoke);margin-bottom:1rem;font-size:1rem;line-height:1.7}

.values-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1.5rem;margin:4rem 0}
.value-card{background:var(--white);border-radius:var(--radius);padding:2rem;transition:var(--transition);border:1px solid transparent}
.value-card:hover{transform:translateY(-4px);border-color:var(--ink);box-shadow:0 12px 40px -16px rgba(14,14,14,.15)}
.value-icon{width:48px;height:48px;background:var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.35rem;margin-bottom:1.25rem}
.value-card h3{font-size:1.15rem;font-weight:500;margin-bottom:.5rem;color:var(--ink)}
.value-card p{color:var(--smoke);font-size:.875rem;line-height:1.6}

.stats-strip{background:var(--ink);color:var(--paper);border-radius:var(--radius-lg);padding:3.5rem 2rem;margin:4rem 0;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:2rem;text-align:center}
.stat-num{font-family:'Fraunces',serif;font-size:3rem;font-weight:500;color:var(--accent);letter-spacing:-.04em;line-height:1;margin-bottom:.5rem}
.stat-label{font-size:.75rem;letter-spacing:.15em;text-transform:uppercase;color:var(--cloud);font-weight:600}

.cta-box{text-align:center;padding:4rem 2rem;background:var(--white);border-radius:var(--radius-lg);box-shadow:0 4px 24px -12px rgba(14,14,14,.08)}
.cta-box h2{font-size:clamp(1.75rem,3vw,2.5rem);font-weight:400;color:var(--ink);letter-spacing:-.04em;margin-bottom:1rem}
.cta-box h2 em{font-style:italic;color:var(--smoke);font-weight:300}
.cta-box p{color:var(--smoke);max-width:480px;margin:0 auto 2rem}

.btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:.9rem 1.75rem;border:none;border-radius:var(--radius-sm);font-size:.9rem;font-weight:600;font-family:inherit;cursor:pointer;text-decoration:none;transition:var(--transition)}
.btn-primary{background:var(--ink);color:var(--paper)}
.btn-primary:hover{background:var(--accent);color:var(--ink);transform:translateY(-2px)}
.btn-secondary{background:transparent;color:var(--ink);border:1.5px solid var(--ink)}
.btn-secondary:hover{background:var(--ink);color:var(--paper)}

.footer{background:var(--ink);color:var(--paper);padding:4rem 2.5rem 2rem;margin-top:5rem}
.footer-inner{max-width:1440px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;gap:2rem;flex-wrap:wrap}
.footer-brand{font-family:'Fraunces',serif;font-size:1.75rem;font-weight:700;color:var(--paper);letter-spacing:-.04em}
.footer-brand::before{content:'';display:inline-block;width:8px;height:8px;background:var(--accent);border-radius:50%;margin-right:.5rem;vertical-align:middle}
.footer p{color:var(--mist);font-size:.85rem}

@keyframes fadeIn{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
@media (max-width:900px){
  .story-grid{grid-template-columns:1fr;gap:2rem}
  .about-hero{padding:3rem 1.25rem 2rem}
}
@media (max-width:768px){
  .nav-container{flex-direction:column;gap:1rem;padding:1rem 1.5rem}
  .nav-links{justify-content:center}
  .container{padding:0 1.25rem 3rem}
  .stats-strip{padding:2.5rem 1.5rem}
  .stat-num{font-size:2.2rem}
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

<section class="about-hero">
    <span class="eyebrow">✦ Our Story</span>
    <h1>Skincare made<br>with <span>intention.</span></h1>
    <p>K-beauty was born from a simple belief: that skincare should be a moment of calm, not a chore. We craft every formula with clean ingredients and radical transparency.</p>
</section>

<div class="container">
    <div class="story-grid">
        <div class="story-visual">
            <div class="story-visual-text">"Clean ingredients.<br>Real results."</div>
        </div>
        <div class="story-content">
            <span class="eyebrow">✦ Our Philosophy</span>
            <h2>Botanical science, <em>beautifully</em> simple.</h2>
            <p>We started K-beauty in 2020 with one goal: make effective skincare that's kind to your skin and the planet. No fillers. No gimmicks. Just formulas that work.</p>
            <p>Every product is dermatologist-tested, cruelty-free, and made in small batches to ensure freshness. We believe your routine should feel like a ritual, not a rush.</p>
        </div>
    </div>

    <div class="values-grid">
        <div class="value-card">
            <div class="value-icon">🌿</div>
            <h3>Clean Formulas</h3>
            <p>Free from parabens, sulfates, and 1,500+ questionable ingredients. Only what your skin needs.</p>
        </div>
        <div class="value-card">
            <div class="value-icon">🐰</div>
            <h3>Cruelty-Free</h3>
            <p>Never tested on animals. Ever. Certified by Leaping Bunny since day one.</p>
        </div>
        <div class="value-card">
            <div class="value-icon">🧪</div>
            <h3>Science-Backed</h3>
            <p>Formulated with dermatologists and backed by clinical studies. Real results you can see.</p>
        </div>
        <div class="value-card">
            <div class="value-icon">♻️</div>
            <h3>Sustainable</h3>
            <p>Recyclable packaging, carbon-neutral shipping, and refill options for every product.</p>
        </div>
    </div>

    <div class="stats-strip">
        <div>
            <div class="stat-num">50K+</div>
            <div class="stat-label">Happy Customers</div>
        </div>
        <div>
            <div class="stat-num">4.9★</div>
            <div class="stat-label">Average Rating</div>
        </div>
        <div>
            <div class="stat-num">100%</div>
            <div class="stat-label">Cruelty-Free</div>
        </div>
        <div>
            <div class="stat-num">30+</div>
            <div class="stat-label">Clean Formulas</div>
        </div>
    </div>

    <div class="cta-box">
        <h2>Ready to <em>glow?</em></h2>
        <p>Discover the collection that's changing how people think about skincare.</p>
        <a href="index.php" class="btn btn-primary">Shop the Collection →</a>
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
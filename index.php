<?php
require_once "config.php";

$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
$total_products = $result->num_rows;
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($search !== '') {
    $s = $conn->real_escape_string($search);
    $result = $conn->query("SELECT * FROM products 
                            WHERE name LIKE '%$s%' OR description LIKE '%$s%' 
                            ORDER BY created_at DESC");
} else {
    $result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
}
$total_products = $result->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>K-beauty — Modern Skincare Store</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,500;9..144,700&family=Manrope:wght@300;400;500;600;700;800&display=swap');
:root{
  --ink:#0e0e0e; --ink-soft:#2a2a2a; --smoke:#6b6b6b; --mist:#a8a8a8;
  --cloud:#e8e6e1; --paper:#f5f3ef; --paper-light:#fbfaf8; --white:#fff;
  --accent:#c4ff4d; --accent-dark:#a8e035; --coral:#ff7a5c; --coral-dark:#e85a3a;
  --lavender:#d4c5f9; --radius-sm:8px; --radius:20px; --radius-lg:32px;
  --radius-full:999px; --ease:cubic-bezier(.22,1,.36,1); --transition:all .5s var(--ease);
}
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
body {
    font-family: 'Manrope', system-ui, sans-serif;
    background: 
        
        #eed8e5;     
    color: var(--ink);
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
    overflow-x: hidden;
}h1,h2,h3{font-family:'Fraunces',Georgia,serif;font-weight:500;letter-spacing:-.03em;line-height:1.05}

.navbar{position:sticky;top:0;z-index:1000;background:rgba(249, 246, 248, 0.93);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-bottom:1px solid rgba(14,14,14,.06)}
.nav-container{max-width:1440px;margin:0 auto;padding:.8rem 2.5rem;display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap}
.logo{font-family:'Fraunces',serif;font-size:1.5rem;font-weight:700;color:var(--ink);text-decoration:none;letter-spacing:-.04em;display:flex;align-items:center;gap:.4rem}
.logo::before{content:'';width:8px;height:8px;background:#E8B4C0;border-radius:50%;display:inline-block}
.nav-links{display:flex;gap:.25rem;align-items:center;flex-wrap:wrap}
.nav-links a{color:var(--ink);text-decoration:none;font-weight:500;font-size:.875rem;padding:.6rem 1.1rem;border-radius:var(--radius-full);transition:var(--transition)}
.nav-links a:hover{background:var(--ink);color:var(--paper)}
.nav-links a:last-child{background:var(--ink);color:var(--paper);padding:.65rem 1.4rem}
.nav-links a:last-child:hover{background:var(--accent);color:var(--ink)}

.container{max-width:1440px;margin:0 auto;padding:0 2.5rem 5rem}

.hero{padding:3rem 2.5rem 5rem;max-width:1440px;margin:0 auto;display:grid;grid-template-columns:1.2fr 1fr;gap:4rem;align-items:center;animation:fadeIn 1s var(--ease) both}
.hero-tag{display:inline-flex;align-items:center;gap:.5rem;background:var(--ink);color:var(--paper);padding:.5rem 1rem;border-radius:var(--radius-full);font-size:.7rem;font-weight:700;letter-spacing:.15em;text-transform:uppercase;margin-bottom:2rem}
.hero-tag::before{content:'';width:6px;height:6px;background:#fff;border-radius:50%}
.hero h1{font-size:clamp(2.5rem,6vw,5rem);font-weight:400;color:var(--ink);margin-bottom:1.5rem;letter-spacing:-.05em;line-height:.95}
.hero h1 em{font-style:italic;font-weight:300;color:var(--ink-soft)}
.hero h1 span{background:#EFC0CB;padding:0 .3em;border-radius:.2em;display:inline-block;transform:rotate(-1deg)}
.hero p{font-size:1.1rem;color:var(--smoke);max-width:480px;margin-bottom:2.5rem}
.hero-actions{display:flex;gap:.75rem;flex-wrap:wrap;align-items:center}
.hero-visual{position:relative;height:520px;border-radius:var(--radius-lg);background:linear-gradient(135deg,var(--lavender) 0%,var(--coral) 100%);overflow:hidden;animation:fadeIn 1.2s var(--ease) both}
.hero-visual::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 30% 30%,rgba(255,255,255,.6) 0,transparent 50%),radial-gradient(circle at 70% 70%,rgba(196,255,77,.5) 0,transparent 50%)}
.hero-visual-text{position:absolute;bottom:2.5rem;left:2.5rem;right:2.5rem;color:var(--white);font-family:'Fraunces',serif;font-size:1.75rem;font-weight:400;line-height:1.2;letter-spacing:-.03em;text-shadow:0 2px 20px rgba(0,0,0,.15)}
.hero-visual-badge{position:absolute;top:2rem;right:2rem;background:var(--white);color:var(--ink);padding:.75rem 1.25rem;border-radius:var(--radius-full);font-size:.75rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;box-shadow:0 8px 32px rgba(0,0,0,.12)}
.hero-visual-shape{position:absolute;border-radius:50%;background:rgba(255,255,255,.25);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.4)}
.shape-1{width:180px;height:180px;top:15%;left:10%;animation:float 6s ease-in-out infinite}
.shape-2{width:100px;height:100px;top:55%;right:15%;animation:float 8s ease-in-out infinite reverse}

@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-20px)}}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.6;transform:scale(.85)}}
@keyframes fadeIn{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
@keyframes fadeUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}

.section-header{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:3rem;gap:2rem;flex-wrap:wrap;margin-top:1rem}
.section-header-left{flex:1;min-width:280px}
.section-eyebrow{display:inline-block;font-size:.7rem;font-weight:700;letter-spacing:.2em;text-transform:uppercase;color:var(--smoke);margin-bottom:.75rem}
.section-title{font-size:clamp(2rem,4vw,3.2rem);font-weight:400;color:var(--ink);letter-spacing:-.045em;line-height:1;margin-bottom:1.25rem}
.section-title em{font-style:italic;color:var(--smoke);font-weight:300}

.search-form{display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.5rem;align-items:stretch}
.search-input{flex:1;min-width:200px;max-width:360px;padding:.75rem 1rem;border:1.5px solid var(--cloud);border-radius:var(--radius-sm);font-size:.875rem;font-family:inherit;background:var(--white);font-weight:500;transition:var(--transition)}
.search-input:focus{outline:none;border-color:var(--ink);box-shadow:0 0 0 4px rgba(196,255,77,.25)}
.search-btn{padding:.75rem 1.15rem;font-size:.8rem;font-weight:700;letter-spacing:.03em;background:var(--ink);color:var(--paper);border:none;border-radius:var(--radius-sm);cursor:pointer;font-family:inherit;transition:var(--transition);white-space:nowrap}
.search-btn:hover{background:#1a1a1a;color:var(--paper)}
.search-clear{padding:.75rem 1rem;font-size:.8rem;font-weight:600;background:transparent;color:var(--ink);border:1.5px solid var(--ink);border-radius:var(--radius-sm);text-decoration:none;transition:var(--transition);white-space:nowrap;display:inline-flex;align-items:center}
.search-clear:hover{background:var(--ink);color:var(--paper)}

.product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:2rem 1.5rem}
.product-card{background:transparent;border-radius:var(--radius);display:flex;flex-direction:column;animation:fadeUp .7s var(--ease) backwards;position:relative; border:1.5px solid transparent; padding:1rem; transition:var(--transition);}
.product-card:hover{border-color:#C77C90;box-shadow:0 12px 40px -20px rgba(243, 42, 126, 0.6);}
.product-image{aspect-ratio:1;background:var(--white);border-radius:var(--radius);overflow:hidden;position:relative;margin-bottom:1.25rem;transition:var(--transition)}
.product-card:hover .product-image{transform:translateY(-6px);box-shadow:0 24px 48px -20px rgba(14,14,14,.15)}
.product-image img{width:100%;height:100%;object-fit:cover;transition:transform .8s var(--ease);display:block}
.product-card:hover .product-image img{transform:scale(1.05)}
.no-image{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-family:'Fraunces',serif;font-size:5rem;font-weight:300;color:var(--cloud);background:var(--paper-light)}
.product-image::after{content:'→';position:absolute;top:1.25rem;right:1.25rem;width:40px;height:40px;background:var(--ink);color:var(--paper);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;opacity:0;transform:translateY(-4px);transition:var(--transition)}
.product-card:hover .product-image::after{opacity:1;transform:translateY(0)}
.product-badge{position:absolute;top:1.25rem;left:1.25rem;background:var(--accent);color:var(--ink);padding:.35rem .85rem;border-radius:var(--radius-full);font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;z-index:2}
.product-badge.soldout{background:var(--ink);color:var(--paper)}
.product-info{padding:0 .25rem}
.product-info h3{font-family:'Fraunces',serif;font-size:1.2rem;font-weight:500;color:var(--ink);margin-bottom:.5rem;line-height:1.25}
.description{color:var(--smoke);font-size:.875rem;margin-bottom:1rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.product-meta{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid var(--cloud)}
.price{font-family:'Fraunces',serif;font-size:1.5rem;font-weight:500;color:var(--ink);letter-spacing:-.03em}
.stock{font-size:.7rem;color:var(--smoke);text-transform:uppercase;letter-spacing:.1em;font-weight:600}
.stock.low{color:var(--coral-dark)}
.stock.low::before{content:'● ';animation:pulse 1.5s ease-in-out infinite}

.add-form{display:flex;gap:.5rem;align-items:stretch}
.qty-input{width:56px;padding:.7rem .4rem;border:1.5px solid var(--cloud);border-radius:var(--radius-sm);font-size:.9rem;font-family:inherit;text-align:center;transition:var(--transition);background:var(--white);font-weight:500}
.qty-input:focus{outline:none;border-color:var(--ink)}

.btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:.75rem 1.5rem;border:none;border-radius:var(--radius-sm);font-size:.875rem;font-weight:600;font-family:inherit;cursor:pointer;text-decoration:none;transition:var(--transition);white-space:nowrap}
.btn-primary{background:var(--ink);color:var(--paper);flex:1}
.btn-primary:hover{background:var(--ink);color:var(--paper);transform:translateY(-2px);box-shadow:0 12px 32px -12px rgba(14,14,14,.35)}
.btn-secondary{background:transparent;color:var(--ink);border:1.5px solid var(--ink)}
.btn-secondary:hover{background:var(--ink);color:var(--paper)}
.btn-disabled{background:var(--cloud);color:var(--mist);cursor:not-allowed;flex:1}
.btn-lg{padding:1.1rem 2rem;font-size:.95rem}

.alert{padding:1rem 1.5rem;border-radius:var(--radius-sm);margin-bottom:2rem;font-weight:500;font-size:.9rem;animation:fadeUp .4s var(--ease);display:flex;align-items:center;gap:.75rem}
.alert::before{content:'';width:8px;height:8px;border-radius:50%;flex-shrink:0}
.alert-success{background:var(--accent);color:var(--ink)}
.alert-success::before{background:var(--ink)}
.alert-error{background:#ffe4dc;color:var(--coral-dark)}
.alert-error::before{background:var(--coral-dark)}

.empty-cart{text-align:center;padding:6rem 2rem;background:var(--white);border-radius:var(--radius-lg);box-shadow:0 4px 24px -12px rgba(14,14,14,.08)}
.empty-cart::before{content:'✧';font-size:5rem;display:block;margin-bottom:1.5rem;color:var(--accent);font-family:'Fraunces',serif}
.empty-cart p{font-family:'Fraunces',serif;font-size:1.5rem;color:var(--ink);margin-bottom:2rem;font-weight:400;letter-spacing:-.03em}

.footer{background:var(--ink);color:var(--paper);padding:4rem 2.5rem 2rem;margin-top:5rem}
.footer-inner{max-width:1440px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;gap:2rem;flex-wrap:wrap}
.footer-brand{font-family:'Fraunces',serif;font-size:1.75rem;font-weight:700;color:var(--paper);letter-spacing:-.04em}
.footer-brand::before{content:'';display:inline-block;width:8px;height:8px;background:var(--accent);border-radius:50%;margin-right:.5rem;vertical-align:middle}
.footer p{color:var(--mist);font-size:.85rem}

@media (max-width:1024px){
  .hero{grid-template-columns:1fr;gap:3rem;padding:4rem 2rem 3rem}
  .hero-visual{height:400px}
}
@media (max-width:768px){
  .nav-container{flex-direction:column;gap:1rem;padding:1rem 1.5rem}
  .nav-links{justify-content:center}
  .container{padding:0 1.25rem 3rem}
  .hero{padding:3rem 1.25rem 2rem}
  .hero h1{font-size:2.5rem}
  .hero-visual{height:320px}
  .hero-visual-text{font-size:1.25rem;bottom:1.5rem;left:1.5rem;right:1.5rem}
  .hero-visual-badge{top:1rem;right:1rem;padding:.5rem .9rem;font-size:.65rem}
  .hero-visual-img{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    width:auto;
    height:auto;
    max-width:100%;
    max-height:100%;
    object-fit:contain;
    z-index:1;
}
  .product-grid{grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1.5rem 1rem}
  .section-count{display:none}
  .search-input{max-width:100%}
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
                <a href="orders.php">Orders</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php if ($search === ''): ?>
<section class="hero">
    <div class="hero-content">
        <span class="hero-tag">New Collection · 2026</span>
        <h1>Skincare<br>that <span>glows</span><br><em>with you.</em></h1>
        <p>Botanical formulas, modern science. Discover a ritual that transforms your skin — and your everyday.</p>
        <div class="hero-actions">
            <a href="#collection" class="btn btn-primary btn-lg">Shop Now →</a>
            <a href="register.php" class="btn btn-secondary btn-lg">Join Free</a>
        </div>
    </div>
    <div class="hero-visual">
        <span class="hero-visual-badge">★ Best Sellers</span>
        <img src="images/hero.jpg" alt="Featured skincare" class="hero-visual-img">
        <div class="hero-visual-text">"Skincare is a moment for yourself."</div>
    </div>
</section>
<?php endif; ?>

<div class="container">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <div class="section-header" id="collection">
        <div class="section-header-left">
            <span class="section-eyebrow"><?php echo $search !== '' ? '✧ Search results' : '✦ The Collection'; ?></span>
            <h2 class="section-title">
                <?php if ($search !== ''): ?>
                    Results for <em>"<?php echo htmlspecialchars($search); ?>"</em>
                <?php else: ?>
                    Essentials for <em>radiant</em> skin
                <?php endif; ?>
            </h2>
            <form method="GET" action="index.php" class="search-form">
                <input type="text" name="q" placeholder="Search products..." 
                       value="<?php echo htmlspecialchars($search); ?>" class="search-input">
                <button type="submit" class="search-btn">Search</button>
                <?php if ($search !== ''): ?>
                    <a href="index.php" class="search-clear">Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if ($total_products === 0): ?>
        <div class="empty-cart">
            <p>No products found</p>
            <a href="index.php" class="btn btn-primary btn-lg">Browse All</a>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php 
            $delay = 0;
            while ($product = $result->fetch_assoc()): 
                $delay += 0.06;
            
                $img = productImage($product['image'], $product['name'], $product['price']);
                $isLow = $product['stock'] > 0 && $product['stock'] < 10;
                $isSoldOut = $product['stock'] <= 0;
            ?>
                <div class="product-card" style="animation-delay: <?php echo $delay; ?>s">
                    <div class="product-image">
                        <?php if ($isSoldOut): ?>
                            <span class="product-badge soldout">Sold Out</span>
                        <?php elseif ($isLow): ?>
                            <span class="product-badge">Low Stock</span>
                        <?php endif; ?>
                        <?php if ($img): ?>
                            <img src="<?php echo htmlspecialchars($img); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            <div class="no-image"><?php echo strtoupper(substr($product['name'], 0, 1)); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="product-meta">
                            <span class="price">Rs. <?php echo number_format($product['price'], 2); ?></span>
                            <span class="stock <?php echo $isLow ? 'low' : ''; ?>">
                                <?php 
                                if ($isSoldOut) echo 'Out of stock';
                                elseif ($isLow) echo $product['stock'].' left';
                                else echo 'In stock';
                                ?>
                            </span>
                        </div>
                        <?php if ($product['stock'] > 0): ?>
                            <form action="cart.php" method="POST" class="add-form">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="qty-input">
                                <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-disabled" disabled>Out of Stock</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<footer class="footer">
    <div class="footer-inner">
        <div class="footer-brand">K-beauty</div>
        <p>© 2026 Glow Skin · Naturally radiant skincare for every ritual</p>
    </div>
</footer>
</body>
</html>
<?php
require_once "config.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();
if (!$product) redirect('index.php');

$reviews = $conn->query("SELECT r.review, u.username FROM product_review r JOIN users u ON r.user_id = u.id WHERE r.product_id = $id ORDER BY r.id DESC");
$img = productImage($product['image'], $product['name'], $product['price']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($product['name']); ?> — K-beauty</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,700&family=Manrope:wght@400;500;600;700&display=swap');
:root{--ink:#0e0e0e;--smoke:#6b6b6b;--cloud:#e8e6e1;--paper:#f5f3ef;--white:#fff;--accent:#c4ff4d;--radius:20px;--radius-sm:8px}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Manrope',system-ui,sans-serif;background:#eed8e5;color:var(--ink);line-height:1.5}
h1,h2,h3{font-family:'Fraunces',Georgia,serif;font-weight:500;letter-spacing:-.03em}
.navbar{background:rgba(249,246,248,.93);border-bottom:1px solid rgba(14,14,14,.06)}
.nav-container{max-width:1100px;margin:0 auto;padding:.8rem 1.5rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem}
.logo{font-family:'Fraunces',serif;font-size:1.5rem;font-weight:700;color:var(--ink);text-decoration:none}
.nav-links a{color:var(--ink);text-decoration:none;font-weight:500;font-size:.875rem;padding:.5rem .9rem}
.container{max-width:1100px;margin:0 auto;padding:1.5rem 1.5rem 4rem}
.back{display:inline-block;margin-bottom:1.5rem;color:var(--ink);font-weight:600;text-decoration:none}
.detail{display:grid;grid-template-columns:1fr 1fr;gap:3rem;margin-bottom:3rem}
.detail img{width:100%;aspect-ratio:1;object-fit:cover;border-radius:var(--radius);background:var(--white)}
.category{font-size:.75rem;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:var(--smoke)}
.detail h1{font-size:2.4rem;line-height:1.1;margin:.5rem 0 1rem}
.price{font-family:'Fraunces',serif;font-size:2rem;margin-bottom:.5rem}
.stock{font-size:.8rem;font-weight:600;color:var(--smoke);margin-bottom:1.5rem}
.description{margin-bottom:1.5rem;color:var(--smoke)}
.buy-form{display:flex;gap:.5rem}
.qty-input{width:70px;padding:.7rem .4rem;border:1.5px solid var(--cloud);border-radius:var(--radius-sm);text-align:center;font-family:inherit}
.btn{padding:.8rem 1.5rem;border:none;border-radius:var(--radius-sm);font-size:.9rem;font-weight:600;font-family:inherit;cursor:pointer;text-decoration:none}
.btn-primary{background:var(--ink);color:var(--paper)}
.btn-secondary{background:transparent;color:var(--ink);border:1.5px solid var(--ink)}
.alert{background:var(--accent);padding:1rem 1.5rem;border-radius:var(--radius-sm);margin-bottom:1.5rem;font-weight:500}
.reviews{background:var(--white);border-radius:var(--radius);padding:2rem}
.reviews h2{margin-bottom:1rem}
.review{display:flex;gap:1rem;padding:1rem;margin-bottom:.75rem;background:var(--paper);border-radius:var(--radius-sm)}
.review p{color:var(--smoke);margin-top:.2rem}
.avatar{flex-shrink:0;width:40px;height:40px;border-radius:50%;background:#E8B4C0;display:flex;align-items:center;justify-content:center;font-weight:700}
.no-reviews{color:var(--smoke)}
.review-form{margin-bottom:1.5rem}
.review-form textarea{width:100%;padding:.8rem;border:1.5px solid var(--cloud);border-radius:var(--radius-sm);font-family:inherit;margin-bottom:.5rem}
@media (max-width:768px){.detail{grid-template-columns:1fr;gap:1.5rem}}
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
                <a href="cart.php">Cart</a>
                <a href="orders.php">Orders</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container">
    <a href="index.php" class="back">← Back to shop</a>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <div class="detail">
        <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        <div>
            <span class="category"><?php echo htmlspecialchars($product['category']); ?></span>
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="price">Rs. <?php echo number_format($product['price'], 2); ?></p>
            <p class="stock"><?php echo $product['stock'] > 0 ? $product['stock'] . ' in stock' : 'Out of stock'; ?></p>
            <p class="description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <?php if ($product['stock'] > 0): ?>
                <form action="cart.php" method="POST" class="buy-form">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="add_to_cart" value="1">
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="qty-input">
                    <button type="submit" class="btn btn-secondary">Add to Cart</button>
                    <button type="submit" name="buy" value="1" class="btn btn-primary">Buy Now</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="reviews">
        <h2>Customer Reviews (<?php echo $reviews->num_rows; ?>)</h2>

        <?php if (isLoggedIn()): ?>
            <form action="review.php" method="POST" class="review-form">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <textarea name="review" maxlength="250" rows="3" placeholder="Share your thoughts about this product..." required></textarea>
                <button type="submit" class="btn btn-primary">Submit Review</button>
            </form>
        <?php else: ?>
            <p class="review-form"><a href="login.php">Login</a> to write a review.</p>
        <?php endif; ?>

        <?php if ($reviews->num_rows === 0): ?>
            <p class="no-reviews">No reviews yet. Be the first to review this product!</p>
        <?php endif; ?>
        <?php while ($rv = $reviews->fetch_assoc()): ?>
            <div class="review">
                <div class="avatar"><?php echo strtoupper(substr($rv['username'], 0, 1)); ?></div>
                <div>
                    <strong><?php echo htmlspecialchars($rv['username']); ?></strong>
                    <p><?php echo htmlspecialchars($rv['review']); ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>

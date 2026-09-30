<?php
require_once 'config.php';
if (!isLoggedIn()) redirect('login.php');

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    $_SESSION['message'] = 'Order not found';
    redirect('orders.php');
}

$items = $conn->query("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Confirmed — Glow Skin</title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='40' fill='%23c4ff4d'/></svg>">
<style>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,700&family=Manrope:wght@300;400;500;600;700;800&display=swap');
:root{--ink:#0e0e0e;--ink-soft:#2a2a2a;--smoke:#6b6b6b;--mist:#a8a8a8;--cloud:#e8e6e1;--paper:#f5f3ef;--paper-light:#fbfaf8;--white:#fff;--accent:#c4ff4d;--accent-dark:#a8e035;--coral:#ff7a5c;--coral-dark:#e85a3a;--radius-sm:8px;--radius:20px;--radius-lg:32px;--radius-full:999px;--ease:cubic-bezier(.22,1,.36,1);--transition:all .5s var(--ease)}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Manrope',system-ui,sans-serif;background:var(--paper);color:var(--ink);line-height:1.5;-webkit-font-smoothing:antialiased}
h1,h2,h3{font-family:'Fraunces',Georgia,serif;font-weight:500;letter-spacing:-.03em;line-height:1.05}
.navbar{position:sticky;top:0;z-index:1000;background:rgba(245,243,239,.85);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-bottom:1px solid rgba(14,14,14,.06)}
.nav-container{max-width:1440px;margin:0 auto;padding:1.25rem 2.5rem;display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap}
.logo{font-family:'Fraunces',serif;font-size:1.5rem;font-weight:700;color:var(--ink);text-decoration:none;letter-spacing:-.04em;display:flex;align-items:center;gap:.4rem}
.logo::before{content:'';width:8px;height:8px;background:var(--accent);border-radius:50%;display:inline-block}
.nav-links{display:flex;gap:.25rem;align-items:center;flex-wrap:wrap}
.nav-links a{color:var(--ink);text-decoration:none;font-weight:500;font-size:.875rem;padding:.6rem 1.1rem;border-radius:var(--radius-full);transition:var(--transition)}
.nav-links a:hover{background:var(--ink);color:var(--paper)}
.nav-links a:last-child{background:var(--ink);color:var(--paper);padding:.65rem 1.4rem}
.nav-links a:last-child:hover{background:var(--accent);color:var(--ink)}
.container{max-width:900px;margin:0 auto;padding:3rem 2.5rem 5rem;text-align:center}
.confirm-icon{width:100px;height:100px;background:var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:3rem;color:var(--ink);margin:0 auto 2rem;animation:pop .6s var(--ease) both}
@keyframes pop{0%{transform:scale(0);opacity:0}70%{transform:scale(1.1)}100%{transform:scale(1);opacity:1}}
.eyebrow{display:inline-block;font-size:.7rem;font-weight:700;letter-spacing:.2em;text-transform:uppercase;color:var(--smoke);margin-bottom:1rem}
.confirm-title{font-size:clamp(2rem,4vw,3rem);font-weight:400;color:var(--ink);letter-spacing:-.045em;line-height:1;margin-bottom:1rem}
.confirm-title em{font-style:italic;color:var(--smoke);font-weight:300}
.confirm-sub{color:var(--smoke);margin-bottom:3rem;font-size:1rem}
.order-box{background:var(--white);border-radius:var(--radius-lg);padding:2rem;box-shadow:0 4px 24px -12px rgba(14,14,14,.08);text-align:left;margin-bottom:2rem}
.order-box-head{display:flex;justify-content:space-between;align-items:center;padding-bottom:1.25rem;border-bottom:1px solid var(--cloud);margin-bottom:1.25rem;flex-wrap:wrap;gap:.5rem}
.order-box-head strong{font-family:'Fraunces',serif;font-size:1.2rem;color:var(--ink)}
.order-box-head span{color:var(--smoke);font-size:.85rem}
.item-row{display:flex;justify-content:space-between;padding:.7rem 0;font-size:.9rem;color:var(--ink-soft);border-bottom:1px dashed var(--cloud)}
.item-row:last-child{border-bottom:none}
.item-row strong{color:var(--ink);font-weight:600}
.total-row{display:flex;justify-content:space-between;margin-top:1.25rem;padding-top:1.25rem;border-top:2px solid var(--ink);font-family:'Fraunces',serif;font-size:1.3rem;color:var(--ink)}
.status-pill{display:inline-block;padding:.35rem .9rem;border-radius:var(--radius-full);font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;background:rgba(255,214,102,.2);color:#8a6d1f}
.actions{display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap;margin-top:2rem}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:.9rem 1.75rem;border:none;border-radius:var(--radius-sm);font-size:.9rem;font-weight:600;font-family:inherit;cursor:pointer;text-decoration:none;transition:var(--transition)}
.btn-primary{background:var(--ink);color:var(--paper)}
.btn-primary:hover{background:var(--accent);color:var(--ink);transform:translateY(-2px)}
.btn-secondary{background:transparent;color:var(--ink);border:1.5px solid var(--ink)}
.btn-secondary:hover{background:var(--ink);color:var(--paper)}
@media (max-width:768px){
  .container{padding:2rem 1.25rem 3rem}
  .order-box{padding:1.5rem}
}
</style>
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo">Glow Skin</a>
        <div class="nav-links">
            <a href="index.php">Shop</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
            <a href="cart.php">Cart</a>
            <a href="orders.php">Orders</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="confirm-icon">✓</div>
    <span class="eyebrow">Order confirmed</span>
    <h1 class="confirm-title">Thank you, <em><?php echo htmlspecialchars($_SESSION['username']); ?>.</em></h1>
    <p class="confirm-sub">Your order has been placed successfully. We'll notify you when it ships.</p>

    <div class="order-box">
        <div class="order-box-head">
            <strong>Order #<?php echo $order['id']; ?></strong>
            <span class="status-pill"><?php echo ucfirst($order['status']); ?></span>
            <span><?php echo date('M d, Y · g:i A', strtotime($order['created_at'])); ?></span>
        </div>
        <?php while ($item = $items->fetch_assoc()): ?>
            <div class="item-row">
                <span><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?></span>
                <strong>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
            </div>
        <?php endwhile; ?>
        <div class="total-row">
            <span>Total</span>
            <span>$<?php echo number_format($order['total'], 2); ?></span>
        </div>
    </div>

    <div class="actions">
        <a href="orders.php" class="btn btn-primary">View My Orders</a>
        <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
    </div>
</div>
</body>
</html>
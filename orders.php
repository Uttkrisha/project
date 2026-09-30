<?php
require_once 'config.php';
if (!isLoggedIn()) redirect('login.php');

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Orders — K-beauty</title>
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
.container{max-width:1440px;margin:0 auto;padding:2rem 2.5rem 5rem}
.page-title{font-size:clamp(2.5rem,5vw,4rem);font-weight:400;color:var(--ink);letter-spacing:-.05em;margin-bottom:3rem;line-height:1;padding-top:2rem}
.page-title em{font-style:italic;color:var(--smoke);font-weight:300}
.alert{padding:1rem 1.5rem;border-radius:var(--radius-sm);margin-bottom:2rem;font-weight:500;font-size:.9rem;display:flex;align-items:center;gap:.75rem}
.alert::before{content:'';width:8px;height:8px;border-radius:50%;flex-shrink:0}
.alert-success{background:var(--accent);color:var(--ink)}
.alert-success::before{background:var(--ink)}
.order-card{background:var(--white);border-radius:var(--radius);box-shadow:0 4px 24px -12px rgba(14,14,14,.08);margin-bottom:1.5rem;overflow:hidden;transition:var(--transition)}
.order-card:hover{transform:translateY(-3px);box-shadow:0 12px 40px -16px rgba(14,14,14,.15)}
.order-header{display:flex;justify-content:space-between;align-items:center;padding:1.5rem 2rem;background:var(--ink);color:var(--paper);flex-wrap:wrap;gap:1rem}
.order-header strong{font-family:'Fraunces',serif;font-size:1.2rem;color:var(--paper);font-weight:500;letter-spacing:-.02em}
.order-header span{color:var(--cloud);font-size:.85rem}
.order-header > strong:last-child{color:var(--accent);font-size:1.15rem}
.order-status{padding:.35rem .9rem;border-radius:var(--radius-full);font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em}
.status-pending{background:rgba(255,214,102,.2);color:#ffd666}
.status-shipped{background:rgba(122,184,255,.2);color:#7ab8ff}
.status-delivered{background:var(--accent);color:var(--ink)}
.order-items-table{width:100%;border-collapse:collapse}
.order-items-table th{background:var(--paper-light);color:var(--smoke);padding:1rem 1.5rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em}
.order-items-table td{padding:1.25rem 1.5rem;border-bottom:1px solid var(--paper);font-size:.9rem;font-weight:500}
.order-items-table tbody tr:last-child td{border-bottom:none}
.empty-cart{text-align:center;padding:6rem 2rem;background:var(--white);border-radius:var(--radius-lg);box-shadow:0 4px 24px -12px rgba(14,14,14,.08)}
.empty-cart::before{content:'✧';font-size:5rem;display:block;margin-bottom:1.5rem;color:var(--accent);font-family:'Fraunces',serif}
.empty-cart p{font-family:'Fraunces',serif;font-size:1.5rem;color:var(--ink);margin-bottom:2rem;font-weight:400;letter-spacing:-.03em}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:.75rem 1.5rem;border:none;border-radius:var(--radius-sm);font-size:.875rem;font-weight:600;font-family:inherit;cursor:pointer;text-decoration:none;transition:var(--transition);white-space:nowrap}
.btn-primary{background:var(--ink);color:var(--paper)}
.btn-primary:hover{background:var(--accent);color:var(--ink);transform:translateY(-2px)}
.btn-lg{padding:1.1rem 2rem;font-size:.95rem}
@media (max-width:768px){
  .container{padding:1.5rem 1.25rem 3rem}
  .order-header{flex-direction:column;align-items:flex-start;padding:1.25rem}
  .order-items-table th,.order-items-table td{padding:.75rem .9rem;font-size:.8rem}
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
            <?php if (isAdmin()): ?><a href="admin.php">Admin</a><?php endif; ?>
            <a href="cart.php">Cart</a>
            <a href="orders.php">Orders</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <h1 class="page-title">Your <em>orders.</em></h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <?php if ($result->num_rows === 0): ?>
        <div class="empty-cart">
            <p>No orders yet</p>
            <a href="index.php" class="btn btn-primary btn-lg">Start Shopping</a>
        </div>
    <?php else: ?>
        <?php while ($order = $result->fetch_assoc()): ?>
            <div class="order-card">
                <div class="order-header">
                    <strong>Order #<?php echo $order['id']; ?></strong>
                    <span class="order-status status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
                    <span><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                    <strong>$<?php echo number_format($order['total'], 2); ?></strong>
                </div>
                <table class="order-items-table">
                    <thead><tr><th>Product</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr></thead>
                    <tbody>
                        <?php
                        $order_id = $order['id'];
                        $items = $conn->query("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id");
                        while ($item = $items->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>
</body>
</html>
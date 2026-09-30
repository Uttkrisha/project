<?php
require_once 'config.php';

if (!isLoggedIn()) {
    $_SESSION['message'] = 'Please login to add items to cart';
    redirect('login.php');
}

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $check = $conn->query("SELECT stock FROM products WHERE id = $product_id");
    if ($check && $row = $check->fetch_assoc()) {
        $current = $_SESSION['cart'][$product_id] ?? 0;
        $_SESSION['cart'][$product_id] = min($current + $quantity, $row['stock']);
        $_SESSION['message'] = 'Added to your cart';
    }
    redirect('cart.php');
}

if (isset($_GET['remove'])) { unset($_SESSION['cart'][(int)$_GET['remove']]); redirect('cart.php'); }
if (isset($_GET['clear']))  { $_SESSION['cart'] = []; redirect('cart.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $id => $qty) {
        $id = (int)$id; $qty = (int)$qty;
        if ($qty <= 0) unset($_SESSION['cart'][$id]);
        else $_SESSION['cart'][$id] = $qty;
    }
    redirect('cart.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if (empty($_SESSION['cart'])) { $_SESSION['message'] = 'Your cart is empty'; redirect('cart.php'); }

    $user_id = $_SESSION['user_id'];
    $total = 0;
    foreach ($_SESSION['cart'] as $pid => $qty) {
        $r = $conn->query("SELECT price FROM products WHERE id = $pid");
        if ($row = $r->fetch_assoc()) $total += $row['price'] * $qty;
    }

    $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'pending')");
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    foreach ($_SESSION['cart'] as $pid => $qty) {
        $r = $conn->query("SELECT price FROM products WHERE id = $pid");
        if ($row = $r->fetch_assoc()) {
            $price = $row['price'];
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $order_id, $pid, $qty, $price);
            $stmt->execute();
            $stmt->close();
            $conn->query("UPDATE products SET stock = stock - $qty WHERE id = $pid");
        }
    }

    $_SESSION['cart'] = [];
    redirect('confirmation.php?order_id=' . $order_id);
}

$cart_items = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $result = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    while ($row = $result->fetch_assoc()) {
        $qty = $_SESSION['cart'][$row['id']];
        $row['quantity'] = $qty;
        $row['subtotal'] = $row['price'] * $qty;
        $total += $row['subtotal'];
        $cart_items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cart — Glow Skin</title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='40' fill='%23c4ff4d'/></svg>">
<style>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,700&family=Manrope:wght@300;400;500;600;700;800&display=swap');
:root{--ink:#0e0e0e;--ink-soft:#2a2a2a;--smoke:#6b6b6b;--mist:#a8a8a8;--cloud:#e8e6e1;--paper:#f5f3ef;--paper-light:#fbfaf8;--white:#fff;--accent:#c4ff4d;--accent-dark:#a8e035;--coral:#ff7a5c;--coral-dark:#e85a3a;--radius-sm:8px;--radius:20px;--radius-lg:32px;--radius-full:999px;--ease:cubic-bezier(.22,1,.36,1);--transition:all .5s var(--ease)}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Manrope',system-ui,sans-serif;background:var(--paper);color:var(--ink);line-height:1.5;-webkit-font-smoothing:antialiased;min-height:100vh}
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
.cart-table{width:100%;border-collapse:collapse;background:var(--white);border-radius:var(--radius);overflow:hidden;box-shadow:0 4px 24px -12px rgba(14,14,14,.08);margin-bottom:1.5rem}
.cart-table th{background:var(--paper-light);color:var(--smoke);padding:1rem 1.5rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em}
.cart-table td{padding:1.25rem 1.5rem;border-bottom:1px solid var(--paper);font-size:.9rem;font-weight:500;vertical-align:middle}
.cart-table tbody tr:last-child td{border-bottom:none}
.cart-table tbody tr:hover{background:var(--paper-light)}
.product-thumb{width:60px;height:60px;border-radius:var(--radius-sm);object-fit:cover;background:var(--paper);border:1px solid var(--cloud)}
.qty-input{width:70px;padding:.6rem .5rem;border:1.5px solid var(--cloud);border-radius:var(--radius-sm);font-size:.9rem;font-family:inherit;text-align:center;background:var(--white);font-weight:500;transition:var(--transition)}
.qty-input:focus{outline:none;border-color:var(--ink)}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:.75rem 1.5rem;border:none;border-radius:var(--radius-sm);font-size:.875rem;font-weight:600;font-family:inherit;cursor:pointer;text-decoration:none;transition:var(--transition);white-space:nowrap}
.btn-primary{background:var(--ink);color:var(--paper)}
.btn-primary:hover{background:var(--accent);color:var(--ink);transform:translateY(-2px)}
.btn-secondary{background:transparent;color:var(--ink);border:1.5px solid var(--ink)}
.btn-secondary:hover{background:var(--ink);color:var(--paper)}
.btn-danger{background:var(--coral);color:var(--white)}
.btn-danger:hover{background:var(--coral-dark)}
.btn-sm{padding:.5rem 1rem;font-size:.78rem}
.cart-actions{display:flex;gap:.75rem;justify-content:flex-end;margin-top:2rem;flex-wrap:wrap}
.text-right{text-align:right}
.empty-cart{text-align:center;padding:6rem 2rem;background:var(--white);border-radius:var(--radius-lg);box-shadow:0 4px 24px -12px rgba(14,14,14,.08)}
.empty-cart::before{content:'✧';font-size:5rem;display:block;margin-bottom:1.5rem;color:var(--accent);font-family:'Fraunces',serif}
.empty-cart p{font-family:'Fraunces',serif;font-size:1.5rem;color:var(--ink);margin-bottom:2rem;font-weight:400;letter-spacing:-.03em}
@media (max-width:768px){
  .container{padding:1.5rem 1.25rem 3rem}
  .cart-table th,.cart-table td{padding:.75rem .9rem;font-size:.8rem}
  .product-thumb{width:44px;height:44px}
  .cart-actions{justify-content:stretch}
  .cart-actions .btn{flex:1}
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
    <h1 class="page-title">Your <em>cart.</em></h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <div class="empty-cart">
            <p>Your cart is empty</p>
            <a href="index.php" class="btn btn-primary">Discover Products</a>
        </div>
    <?php else: ?>
        <form method="POST" action="cart.php">
            <table class="cart-table">
                <thead>
                    <tr><th></th><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item):
                        $img = productImage($item['image'], $item['name'], $item['price']);
                    ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($img); ?>" class="product-thumb" alt=""></td>
                            <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td><input type="number" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" class="qty-input"></td>
                            <td><strong>$<?php echo number_format($item['subtotal'], 2); ?></strong></td>
                            <td><a href="cart.php?remove=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger">Remove</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Total</strong></td>
                        <td colspan="2"><strong style="font-family:'Fraunces',serif;font-size:1.5rem">$<?php echo number_format($total, 2); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            <div class="cart-actions">
                <a href="cart.php?clear=1" class="btn btn-secondary" onclick="return confirm('Clear all items?')">Clear Cart</a>
                <button type="submit" name="update_cart" class="btn btn-secondary">Update</button>
                <button type="submit" name="place_order" class="btn btn-primary">Place Order →</button>
            </div>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
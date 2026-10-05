<?php
require_once 'config.php';
if (!isAdmin()) redirect('index.php');

$error = '';
$success = '';

/* ---------- CREATE PRODUCT ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $image = 'serum';

    if (empty($name) || $price <= 0) {
        $error = 'Name and valid price are required';
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed = ['jpg','jpeg','png','gif','webp','svg'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                if (!is_dir('images')) mkdir('images', 0755, true);
                $image = uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], 'images/' . $image);
            } else {
                $error = 'Only JPG, PNG, GIF, WEBP, SVG files allowed';
            }
        } else {
            /* Fallback: category keyword → matching SVG placeholder */
            $lower = strtolower($name);
            if (strpos($lower, 'serum') !== false) $image = 'serum';
            elseif (strpos($lower, 'cream') !== false) $image = 'cream';
            elseif (strpos($lower, 'cleanser') !== false) $image = 'cleanser';
            elseif (strpos($lower, 'oil') !== false) $image = 'oil';
            elseif (strpos($lower, 'spf') !== false || strpos($lower, 'sun') !== false) $image = 'sunscreen';
            elseif (strpos($lower, 'mask') !== false) $image = 'mask';
            elseif (strpos($lower, 'toner') !== false) $image = 'toner';
            elseif (strpos($lower, 'retinol') !== false) $image = 'retinol';
        }

        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdis", $name, $description, $price, $stock, $image);
            if ($stmt->execute()) $success = 'Product created successfully';
            else $error = 'Failed to create product';
            $stmt->close();
        }
    }
}

/* ---------- UPDATE PRODUCT ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];

    if (empty($name) || $price <= 0) $error = 'Name and valid price are required';
    else {
        /* Handle optional replacement image */
        $newImage = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed = ['jpg','jpeg','png','gif','webp','svg'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                if (!is_dir('images')) mkdir('images', 0755, true);
                $newImage = uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], 'images/' . $newImage);
            }
        }

        if ($newImage !== null) {
            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, image=? WHERE id=?");
            $stmt->bind_param("ssdisi", $name, $description, $price, $stock, $newImage, $id);
        } else {
            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock=? WHERE id=?");
            $stmt->bind_param("ssdii", $name, $description, $price, $stock, $id);
        }

        if ($stmt->execute()) $success = 'Product updated successfully';
        else $error = 'Failed to update product';
        $stmt->close();
    }
}

/* ---------- DELETE PRODUCT ---------- */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    try {
        // Delete uploaded product image file if it exists
        $img_stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $img_stmt->bind_param("i", $id);
        $img_stmt->execute();
        $img_res = $img_stmt->get_result();
        if ($img_row = $img_res->fetch_assoc()) {
            $img_name = $img_row['image'];
            $protected = ['hero.jpg', 'about.jpg', 'a.jpg', 'h.jpg', 'sss.png'];
            if (!empty($img_name) && !in_array($img_name, $protected)) {
                $file = __DIR__ . '/images/' . $img_name;
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
        }
        $img_stmt->close();

        // Delete referencing order items first to satisfy foreign key constraints
        $stmt_items = $conn->prepare("DELETE FROM order_items WHERE product_id = ?");
        $stmt_items->bind_param("i", $id);
        $stmt_items->execute();
        $stmt_items->close();

        // Delete product from database
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success = 'Product deleted successfully from database';
        } else {
            $error = 'Failed to delete product';
        }
        $stmt->close();
    } catch (Exception $e) {
        $error = 'Failed to delete product: ' . $e->getMessage();
    }
}

/* ---------- UPDATE ORDER STATUS ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    $stmt->close();
    $success = 'Order status updated';
}

$edit_product = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit_product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();
}

$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
$orders = $conn->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
$stats_products = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];
$stats_orders = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$stats_users = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='customer'")->fetch_assoc()['c'];
$stats_revenue = $conn->query("SELECT COALESCE(SUM(total),0) as s FROM orders")->fetch_assoc()['s'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — K-beauty</title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='40' fill='%23c4ff4d'/></svg>">
<style>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,700&family=Manrope:wght@300;400;500;600;700;800&display=swap');
:root{--ink:#0e0e0e;--ink-soft:#2a2a2a;--smoke:#6b6b6b;--mist:#a8a8a8;--cloud:#e8e6e1;--paper:#f5f3ef;--paper-light:#fbfaf8;--white:#fff;--accent:#c4ff4d;--accent-dark:#a8e035;--coral:#ff7a5c;--coral-dark:#e85a3a;--radius-sm:8px;--radius:20px;--radius-lg:32px;--radius-full:999px;--ease:cubic-bezier(.22,1,.36,1);--transition:all .5s var(--ease)}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Manrope',system-ui,sans-serif;background:#eed8e5;color:var(--ink);line-height:1.5;-webkit-font-smoothing:antialiased}
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
.container{max-width:1440px;margin:0 auto;padding:2rem 2.5rem 5rem}
.page-title{font-size:clamp(2.5rem,5vw,4rem);font-weight:400;color:var(--ink);letter-spacing:-.05em;margin-bottom:3rem;line-height:1;padding-top:2rem}
.page-title em{font-style:italic;color:var(--smoke);font-weight:300}
.alert{padding:1rem 1.5rem;border-radius:var(--radius-sm);margin-bottom:2rem;font-weight:500;font-size:.9rem;display:flex;align-items:center;gap:.75rem}
.alert::before{content:'';width:8px;height:8px;border-radius:50%;flex-shrink:0}
.alert-success{background:var(--accent);color:var(--ink)}
.alert-success::before{background:var(--ink)}
.alert-error{background:#ffe4dc;color:var(--coral-dark)}
.alert-error::before{background:var(--coral-dark)}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:3rem}
.stat-card{background:var(--white);border-radius:var(--radius);padding:1.75rem;display:flex;flex-direction:column;gap:.75rem;transition:var(--transition);border:1px solid transparent}
.stat-card:hover{transform:translateY(-4px);border-color:var(--ink)}
.stat-card:nth-child(1){background:var(--ink);color:var(--paper)}
.stat-card:nth-child(1) .stat-label{color:var(--cloud)}
.stat-card:nth-child(1) .stat-value{color:var(--accent)}
.stat-label{font-size:.7rem;text-transform:uppercase;letter-spacing:.15em;color:var(--smoke);font-weight:700}
.stat-value{font-family:'Fraunces',serif;font-size:2.2rem;color:var(--ink);font-weight:500;letter-spacing:-.04em;line-height:1}
.admin-grid{display:grid;grid-template-columns:1fr 1.5fr;gap:1.5rem;margin-bottom:1.5rem}
.admin-card{background:var(--white);border-radius:var(--radius);padding:2rem;box-shadow:0 4px 24px -12px rgba(14,14,14,.08);margin-bottom:1.5rem}
.admin-card h2{font-size:1.35rem;font-weight:500;color:var(--ink);margin-bottom:1.5rem;letter-spacing:-.03em;display:flex;align-items:center;gap:.75rem}
.admin-card h2::before{content:'';width:4px;height:20px;background:var(--accent);border-radius:2px}
.form-group{margin-bottom:1.3rem}
.form-group label{display:block;margin-bottom:.5rem;font-weight:600;color:var(--ink);font-size:.75rem;letter-spacing:.08em;text-transform:uppercase}
.form-group input,.form-group textarea,.form-group select{width:100%;padding:.9rem 1rem;border:1.5px solid var(--cloud);border-radius:var(--radius-sm);font-size:.9rem;font-family:inherit;background:var(--paper-light);transition:var(--transition);color:var(--ink);font-weight:500}
.form-group input:focus,.form-group textarea:focus,.form-group select:focus{outline:none;border-color:var(--ink);background:var(--white);box-shadow:0 0 0 4px rgba(196,255,77,.25)}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:.75rem 1.5rem;border:none;border-radius:var(--radius-sm);font-size:.875rem;font-weight:600;font-family:inherit;cursor:pointer;text-decoration:none;transition:var(--transition);white-space:nowrap}
.btn-primary{background:var(--ink);color:var(--paper)}
.btn-primary:hover{background:var(--accent);color:var(--ink);transform:translateY(-2px)}
.btn-secondary{background:transparent;color:var(--ink);border:1.5px solid var(--ink)}
.btn-secondary:hover{background:var(--ink);color:var(--paper)}
.btn-danger{background:var(--coral);color:var(--white)}
.btn-danger:hover{background:var(--coral-dark)}
.btn-sm{padding:.5rem 1rem;font-size:.78rem}
.btn-block{width:100%;padding:.95rem}
.admin-table{width:100%;border-collapse:collapse;background:var(--white);border-radius:var(--radius);overflow:hidden;box-shadow:0 4px 24px -12px rgba(14,14,14,.08);margin-bottom:1rem}
.admin-table th{background:var(--paper-light);color:var(--smoke);padding:1rem 1.25rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em}
.admin-table td{padding:1.15rem 1.25rem;border-bottom:1px solid var(--paper);font-size:.88rem;font-weight:500;vertical-align:middle}
.admin-table tbody tr:last-child td{border-bottom:none}
.admin-table tbody tr:hover{background:var(--paper-light)}
.product-thumb{width:48px;height:48px;border-radius:var(--radius-sm);object-fit:cover;background:var(--paper);border:1px solid var(--cloud)}
.inline-form{display:flex;gap:.5rem;align-items:center}
.inline-form select{padding:.5rem .75rem;border:1.5px solid var(--cloud);border-radius:var(--radius-sm);font-size:.8rem;font-family:inherit;background:var(--paper-light);font-weight:500}
.inline-form select:focus{outline:none;border-color:var(--ink)}
.order-status{padding:.3rem .8rem;border-radius:var(--radius-full);font-size:.66rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em}
.status-pending{background:#fdecc8;color:#8a6d1f}
.status-shipped{background:#cfe4f7;color:#2c5f8a}
.status-delivered{background:var(--accent);color:var(--ink)}
.current-img{display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;padding:.75rem;background:var(--paper-light);border-radius:var(--radius-sm);border:1px solid var(--cloud)}
.current-img img{width:56px;height:56px;border-radius:var(--radius-sm);object-fit:cover;background:var(--white)}
.current-img span{font-size:.8rem;color:var(--smoke);font-weight:500}
@media (max-width:1024px){.admin-grid{grid-template-columns:1fr}}
@media (max-width:768px){
  .container{padding:1.5rem 1.25rem 3rem}
  .form-row{grid-template-columns:1fr}
  .admin-table th,.admin-table td{padding:.7rem .8rem;font-size:.78rem}
  .inline-form{flex-direction:column;align-items:stretch}
  .stat-value{font-size:1.8rem}
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
            <a href="admin.php">Admin</a>
            <a href="cart.php">Cart</a>
            <a href="orders.php">Orders</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <h1 class="page-title">Admin <em>dashboard.</em></h1>

    <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card"><span class="stat-label">Products</span><span class="stat-value"><?php echo $stats_products; ?></span></div>
        <div class="stat-card"><span class="stat-label">Orders</span><span class="stat-value"><?php echo $stats_orders; ?></span></div>
        <div class="stat-card"><span class="stat-label">Customers</span><span class="stat-value"><?php echo $stats_users; ?></span></div>
        <div class="stat-card"><span class="stat-label">Revenue</span><span class="stat-value">Rs. <?php echo number_format($stats_revenue, 0); ?></span></div>
    </div>

    <div class="admin-grid">
        <div class="admin-card">
            <h2><?php echo $edit_product ? 'Edit Product' : 'Add Product'; ?></h2>
            <form method="POST" action="admin.php" enctype="multipart/form-data">
                <?php if ($edit_product): ?><input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>"><?php endif; ?>

                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"><?php echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Price (Rs.)</label>
                        <input type="number" name="price" step="0.01" min="1" value="<?php echo $edit_product ? (int)$edit_product['price'] : ''; ?>" required placeholder="e.g. 2000">
                    </div>
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" min="0" value="<?php echo $edit_product ? (int)$edit_product['stock'] : '0'; ?>">
                    </div>
                </div>

                <?php if ($edit_product): ?>
                    <?php 
                    $currentImg = productImage($edit_product['image'], $edit_product['name'], $edit_product['price']);
                    ?>
                    <div class="form-group">
                        <label>Current Image</label>
                        <div class="current-img">
                            <img src="<?php echo $currentImg; ?>" alt="">
                            <span>Upload a new file below to replace it (optional)</span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label><?php echo $edit_product ? 'Replace Image (optional)' : 'Image (optional)'; ?></label>
                    <input type="file" name="image" accept="image/*">
                </div>

                <?php if ($edit_product): ?>
                    <button type="submit" name="update" class="btn btn-primary btn-block">Save Changes</button>
                    <a href="admin.php" class="btn btn-secondary btn-block" style="margin-top:.5rem">Cancel</a>
                <?php else: ?>
                    <button type="submit" name="create" class="btn btn-primary btn-block">Add Product</button>
                <?php endif; ?>
            </form>
        </div>

        <div class="admin-card">
            <h2>Products (<?php echo $products->num_rows; ?>)</h2>
            <div style="overflow-x:auto">
            <table class="admin-table">
                <thead><tr><th></th><th>Name</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php while ($p = $products->fetch_assoc()): 
                        $img = productImage($p['image'], $p['name'], $p['price']);
                    ?>
                        <tr>
                            <td><img src="<?php echo $img; ?>" class="product-thumb" alt=""></td>
                            <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                            <td>Rs. <?php echo number_format($p['price'], 2); ?></td>
                            <td><?php echo $p['stock']; ?></td>
                            <td>
                                <a href="admin.php?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                                <a href="admin.php?delete=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h2>Orders (<?php echo $orders->num_rows; ?>)</h2>
        <?php if ($orders->num_rows === 0): ?>
            <p style="color:var(--smoke);text-align:center;padding:3rem;font-family:'Fraunces',serif;font-size:1.25rem">No orders yet</p>
        <?php else: ?>
        <div style="overflow-x:auto">
        <table class="admin-table">
            <thead><tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
                <?php while ($o = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?php echo $o['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($o['username']); ?></td>
                        <td>Rs. <?php echo number_format($o['total'], 2); ?></td>
                        <td><span class="order-status status-<?php echo $o['status']; ?>"><?php echo ucfirst($o['status']); ?></span></td>
                        <td><?php echo date('M d', strtotime($o['created_at'])); ?></td>
                        <td>
                            <form method="POST" action="admin.php" class="inline-form">
                                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                <select name="status">
                                    <option value="pending" <?php echo $o['status']==='pending'?'selected':''; ?>>Pending</option>
                                    <option value="shipped" <?php echo $o['status']==='shipped'?'selected':''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $o['status']==='delivered'?'selected':''; ?>>Delivered</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-primary">Save</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
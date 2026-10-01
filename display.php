<?php
require_once 'config.php';
$result = $conn->query("SELECT id, name, price FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Products</title></head>
<body>
<h1>Products</h1>

<?php while ($p = $result->fetch_assoc()): ?>
    <div style="display:inline-block;margin:1rem;text-align:center">
    
        <img src="serve_image.php?id=<?php echo $p['id']; ?>" 
             alt="<?php echo htmlspecialchars($p['name']); ?>"
             style="width:200px;height:200px;object-fit:cover;border-radius:12px">
        <h3><?php echo htmlspecialchars($p['name']); ?></h3>
        <p>Rs. <?php echo number_format($p['price'], 2); ?></p>
    </div>
<?php endwhile; ?>

</body>
</html>
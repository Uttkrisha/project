<?php
require_once "config.php";

$result = $conn->query("SELECT id, name, price, image FROM products");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
</head>
<body>

<h1>Products</h1>

<?php while ($product = $result->fetch_assoc()): ?>

    <div style="display:inline-block; margin:20px; text-align:center;">

        <img 
            src="images/<?php echo htmlspecialchars($product['image']); ?>"
            width="200"
            height="200"
            style="object-fit:cover;"
        >

        <h3><?php echo htmlspecialchars($product['name']); ?></h3>

        <p>Rs. <?php echo $product['price']; ?></p>

    </div>

<?php endwhile; ?>

</body>
</html>
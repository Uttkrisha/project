<?php
include "db.php";

$sql = "SELECT name, image FROM products";
$result = mysqli_query($conn, $sql);

while ($product = mysqli_fetch_assoc($result)) {
?>

    <div class="product">
        <h3><?php echo htmlspecialchars($product['name']); ?></h3>

        <img 
            src="<?php echo htmlspecialchars($product['image']); ?>" 
            alt="<?php echo htmlspecialchars($product['name']); ?>"
            width="200"
        >
    </div>

<?php
}
?>
<?php
require_once 'config.php';

echo "<h2>Image Debug</h2>";
echo "<p>Images folder: <strong>" . __DIR__ . '/images' . "</strong></p>";
echo "<p>Folder exists: <strong>" . (is_dir(__DIR__.'/images') ? 'YES' : 'NO') . "</strong></p>";
echo "<hr>";

$result = $conn->query("SELECT id, name, image FROM products");
echo "<table border='1' cellpadding='8' style='border-collapse:collapse'>";
echo "<tr><th>ID</th><th>Name</th><th>Image Column</th><th>File Exists?</th><th>Preview</th></tr>";

while ($row = $result->fetch_assoc()) {
    $filePath = __DIR__ . '/images/' . $row['image'];
    $exists = file_exists($filePath);
    $src = productImage($row['image'], $row['name'], 0);

    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td><code>" . htmlspecialchars($row['image']) . "</code></td>";
    echo "<td>" . ($exists ? '✅ YES' : '❌ NO') . "</td>";
    echo "<td><img src='" . $src . "' style='width:60px;height:60px;object-fit:cover;border-radius:6px'></td>";
    echo "</tr>";
}
echo "</table>";
?>
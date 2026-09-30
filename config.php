<?php
session_start();

$conn = new mysqli("localhost", "root", "", "skincare_store");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
function redirect($url) {
    header("Location: $url");
    exit;
}

// Returns a usable image src: full URL, local images/ file, or a placeholder
function productImage($image, $name = '', $price = 0) {
    $image = trim((string)$image);
    if (preg_match('#^https?://#i', $image)) return $image;
    if ($image !== '' && file_exists(__DIR__ . '/images/' . $image)) return 'images/' . rawurlencode($image);
    $letter = strtoupper(substr($name, 0, 1)) ?: '?';
    return 'data:image/svg+xml,' . rawurlencode("<svg xmlns='http://www.w3.org/2000/svg' width='200' height='200'><rect width='200' height='200' fill='#f5f3ef'/><text x='100' y='125' font-size='80' text-anchor='middle' fill='#a8a8a8'>$letter</text></svg>");
}
?>
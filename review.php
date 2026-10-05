<?php
require_once 'config.php';

if (!isLoggedIn()) {
    $_SESSION['message'] = 'Please login to write a review';
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];
    $review = substr(trim($_POST['review']), 0, 250);

    if ($review !== '') {
        $stmt = $conn->prepare("INSERT INTO product_review (user_id, product_id, review) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $product_id, $review);
        $stmt->execute();
        $stmt->close();
        $_SESSION['message'] = 'Thanks for your review';
    }
}
redirect('product.php?id=' . (int)$_POST['product_id']);

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
?>
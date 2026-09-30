<?php

$conn = mysqli_connect("localhost", "root", "", "skincare_store");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
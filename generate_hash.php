<?php
// Visit http://localhost/skincare_store/generate_hash.php ONCE
// Copy the generated hash and update the admin user in phpMyAdmin:
//   UPDATE users SET password = 'PASTE_HASH_HERE' WHERE username = 'admin';
// Then DELETE this file.
echo password_hash('admin123', PASSWORD_DEFAULT);
?>
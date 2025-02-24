<?php
$password = 'password123';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo "Hash generado: " . $hashedPassword;
?>
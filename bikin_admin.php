<?php
require_once 'config/functions.php'; 

$username = 'admin';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$role = 'admin';

try {
    // Mereset password dan memastikan role-nya adalah admin
    $stmt = $pdo->prepare("UPDATE users SET password = ?, role = ? WHERE username = ?");
    $stmt->execute([$password, $role, $username]);

    echo "<b>Password Admin Berhasil Di-reset!</b><br>";
    echo "Username: <b>admin</b><br>";
    echo "Password Baru: <b>admin123</b>";
} catch (Exception $e) {
    echo "Gagal update admin: " . $e->getMessage();
}
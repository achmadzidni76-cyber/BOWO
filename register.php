<?php
require_once 'config/functions.php';

if (isset($_SESSION['user_id'])) {
    header($_SESSION['role'] === 'admin' ? "Location: admin/dashboard.php" : "Location: user/dashboard.php");
    exit;
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = sanitize($_POST['name']);
    $username = sanitize($_POST['username']);
    $email    = sanitize($_POST['email']);
    $phone    = sanitize($_POST['phone']);
    $address  = sanitize($_POST['address']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);

    if ($stmt->rowCount() > 0) {
        $error = "Username atau email sudah terdaftar!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $insert = $pdo->prepare("INSERT INTO users (name, username, email, phone, address, password, role) VALUES (?, ?, ?, ?, ?, ?, 'nasabah')");
        if ($insert->execute([$name, $username, $email, $phone, $address, $hashed_password])) {
            $new_user_id = $pdo->lastInsertId();
            $pdo->prepare("INSERT INTO targets (user_id, target_weight) VALUES (?, 10.00)")->execute([$new_user_id]);
            $success = "Pendaftaran berhasil! Silakan login.";
        } else {
            $error = "Gagal mendaftar, silakan coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akun — BOWO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-emerald-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 border border-emerald-100 my-8">
        <div class="text-center mb-6">
            <a href="index.php" class="inline-block">
                <img src="assets/images/bowo.png" alt="BOWO Logo" class="h-16 w-auto mx-auto mb-2 object-contain">
                <h1 class="text-2xl font-extrabold text-emerald-600">Daftar Nasabah BOWO</h1>
            </a>
            <p class="text-slate-500 text-xs mt-1">Bergabunglah menjaga lingkungan bersama kami</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2.5 rounded-lg mb-4 text-sm"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-2.5 rounded-lg mb-4 text-sm"><?= $success ?> <a href="login.php" class="underline font-bold">Login di sini</a></div>
        <?php endif; ?>

        <form method="POST" class="space-y-3 text-sm">
            <div>
                <label class="block font-medium text-slate-700">Nama Lengkap</label>
                <input type="text" name="name" required class="mt-1 w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block font-medium text-slate-700">Username</label>
                <input type="text" name="username" required class="mt-1 w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block font-medium text-slate-700">Email</label>
                <input type="email" name="email" required class="mt-1 w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block font-medium text-slate-700">Nomor Telepon / WA</label>
                <input type="text" name="phone" required class="mt-1 w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block font-medium text-slate-700">Alamat Lengkap</label>
                <textarea name="address" required rows="2" class="mt-1 w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            <div>
                <label class="block font-medium text-slate-700">Password</label>
                <input type="password" name="password" required class="mt-1 w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
            </div>
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-lg transition mt-2">
                Daftar Akun
            </button>
        </form>

        <p class="mt-4 text-center text-xs text-slate-600">
            Sudah punya akun? <a href="login.php" class="text-emerald-600 font-bold hover:underline">Login Sekarang</a>
        </p>
    </div>
</body>
</html>
<?php
require_once 'config/functions.php';

if (isset($_SESSION['user_id'])) {
    header($_SESSION['role'] === 'admin' ? "Location: admin/dashboard.php" : "Location: user/dashboard.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = sanitize($_POST['identity']);
    $password = $_POST['password'];

    // PERBAIKAN: Menggunakan nama parameter unik (:username dan :email)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = :username OR email = :email) AND status = 'active'");
    $stmt->execute([
        'username' => $identity,
        'email'    => $identity
    ]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        header($user['role'] === 'admin' ? "Location: admin/dashboard.php" : "Location: user/dashboard.php");
        exit;
    } else {
        $error = "Username/email atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login — BOWO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-emerald-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 border border-emerald-100">
        <div class="text-center mb-6">
            <a href="index.php" class="inline-block">
                <img src="assets/images/bowo.png" alt="BOWO Logo" class="h-16 w-auto mx-auto mb-2 object-contain">
                <h1 class="text-3xl font-black text-emerald-600">BOWO</h1>
            </a>
            <p class="text-slate-500 mt-1 text-sm">Bank of Waste Online</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700">Username / Email</label>
                <input type="text" name="identity" required class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Password</label>
                <input type="password" name="password" required class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-lg transition duration-200">
                Masuk
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            Belum punya akun? <a href="register.php" class="text-emerald-600 font-bold hover:underline">Daftar Sekarang</a>
        </p>
    </div>
</body>
</html>
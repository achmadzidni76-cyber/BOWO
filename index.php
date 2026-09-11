<?php
require_once 'config/functions.php';

$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'nasabah'")->fetchColumn();
$total_waste = $pdo->query("SELECT IFNULL(SUM(weight), 0) FROM deposits WHERE status = 'diverifikasi'")->fetchColumn();
$total_deposits = $pdo->query("SELECT COUNT(*) FROM deposits WHERE status = 'diverifikasi'")->fetchColumn();

$waste_types = $pdo->query("SELECT * FROM waste_types WHERE status = 'active'")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOWO — Bank of Waste Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800">

    <!-- Navbar -->
    <nav class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-emerald-100">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-3">
                <span class="text-2xl font-black text-emerald-600 tracking-tight">BOWO</span>
            </a>
            <div class="hidden md:flex space-x-6 text-slate-600 font-medium text-sm">
                <a href="#beranda" class="hover:text-emerald-600 transition">Beranda</a>
                <a href="#tentang" class="hover:text-emerald-600 transition">Tentang</a>
                <a href="#cara-kerja" class="hover:text-emerald-600 transition">Cara Kerja</a>
                <a href="#jenis-sampah" class="hover:text-emerald-600 transition">Jenis Sampah</a>
                <a href="leaderboard.php" class="hover:text-emerald-600 transition">Leaderboard</a>
            </div>
            <div class="flex items-center space-x-3">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= $_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php' ?>" class="px-4 py-2 bg-emerald-600 text-white rounded-lg font-semibold text-sm">Dashboard</a>
                <?php else: ?>
                    <a href="login.php" class="px-4 py-2 text-emerald-600 border border-emerald-600 rounded-lg hover:bg-emerald-50 font-semibold transition text-sm">Login</a>
                    <a href="register.php" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-semibold transition text-sm">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Beranda Hero Section -->
    <section id="beranda" class="py-20 bg-gradient-to-br from-emerald-50 via-teal-50 to-white">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-700 font-semibold rounded-full text-xs mb-4">Eco-Tech Ecosystem</span>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 leading-tight">Ubah Sampah Jadi Tabungan Berharga.</h1>
                <p class="text-slate-600 mt-4 text-base md:text-lg">Kelola sampah dari rumah dengan mudah, dapatkan saldo tabungan, serta poin kontribusi untuk kelestarian lingkungan.</p>
                <div class="mt-8 flex gap-4">
                    <a href="register.php" class="px-6 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition">Mulai Sekarang</a>
                    <a href="#tentang" class="px-6 py-3 bg-white text-slate-700 font-semibold rounded-xl border hover:bg-slate-50 transition">Pelajari BOWO</a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100 text-center">
                    <div class="text-3xl font-black text-emerald-600"><?= number_format($total_users) ?>+</div>
                    <div class="text-xs text-slate-500 font-medium mt-1">Nasabah Aktif</div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100 text-center">
                    <div class="text-3xl font-black text-emerald-600"><?= number_format($total_waste, 1) ?> kg</div>
                    <div class="text-xs text-slate-500 font-medium mt-1">Sampah Terdaur Ulang</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Tentang BOWO -->
    <section id="tentang" class="py-16 bg-white border-t border-slate-100">
        <div class="max-w-5xl mx-auto px-6">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-slate-900">Tentang BOWO</h2>
                <p class="text-slate-500 text-sm mt-2">Bank of Waste Online - Solusi Cerdas Pengelolaan Sampah</p>
            </div>
            <div class="bg-emerald-50/60 p-8 rounded-2xl border border-emerald-100 text-slate-700 leading-relaxed space-y-4">
                <p>
                    <strong>BOWO (Bank of Waste Online)</strong> adalah platform digital terpadu yang dirancang untuk memodernisasi sistem bank sampah. Kami berkomitmen membantu masyarakat mengelola sampah anorganik rumah tangga secara terstruktur dan transparan.
                </p>
                <p>
                    Melalui BOWO, setiap gram sampah yang disetorkan tidak hanya membantu mengurangi dampak pencemaran lingkungan, tetapi juga dikonversi menjadi saldo bernilai ekonomi serta poin penghargaan bagi para nasabah.
                </p>
            </div>
        </div>
    </section>

    <!-- Section Cara Kerja -->
    <section id="cara-kerja" class="py-16 bg-slate-50 border-t border-slate-200/60">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-slate-900">Cara Kerja BOWO</h2>
                <p class="text-slate-500 text-sm mt-2">4 Langkah mudah menyetor sampah dan mendapatkan saldo</p>
            </div>
            <div class="grid md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 relative">
                    <span class="w-8 h-8 bg-emerald-600 text-white rounded-lg flex items-center justify-center font-extrabold text-sm mb-4">01</span>
                    <h3 class="font-bold text-slate-800 text-lg mb-2">Pilah Sampah</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Pilah sampah anorganik (botol plastik, kardus, kertas, logam) di rumah dalam keadaan bersih.</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 relative">
                    <span class="w-8 h-8 bg-emerald-600 text-white rounded-lg flex items-center justify-center font-extrabold text-sm mb-4">02</span>
                    <h3 class="font-bold text-slate-800 text-lg mb-2">Isi Form Setor</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Masuk ke akun nasabah dan isi form pengajuan setoran sampah sesuai estimasi berat.</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 relative">
                    <span class="w-8 h-8 bg-emerald-600 text-white rounded-lg flex items-center justify-center font-extrabold text-sm mb-4">03</span>
                    <h3 class="font-bold text-slate-800 text-lg mb-2">Penimbangan & Verifikasi</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Bawa sampah ke lokasi Bank Sampah BOWO untuk ditimbang dan diverifikasi oleh Admin.</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 relative">
                    <span class="w-8 h-8 bg-emerald-600 text-white rounded-lg flex items-center justify-center font-extrabold text-sm mb-4">04</span>
                    <h3 class="font-bold text-slate-800 text-lg mb-2">Cairkan Saldo & Poin</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Setelah disetujui, saldo rupiah dan poin kontribusi langsung bertambah di akun kamu.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Jenis Sampah -->
    <section id="jenis-sampah" class="py-16 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl font-bold text-slate-900 text-center mb-10">Jenis & Harga Sampah</h2>
            <div class="grid md:grid-cols-4 gap-6">
                <?php foreach ($waste_types as $type): ?>
                    <div class="border rounded-2xl p-6 bg-slate-50 border-slate-200">
                        <span class="text-xs font-semibold uppercase text-emerald-600 bg-emerald-100 px-2.5 py-1 rounded-full"><?= htmlspecialchars($type['category']) ?></span>
                        <h3 class="font-bold text-xl mt-3 text-slate-800"><?= htmlspecialchars($type['name']) ?></h3>
                        <p class="text-sm text-slate-500 mt-1"><?= htmlspecialchars($type['description']) ?></p>
                        <div class="mt-4 pt-4 border-t flex justify-between items-center">
                            <span class="text-slate-500 text-sm">Harga/Kg</span>
                            <span class="text-emerald-600 font-extrabold text-lg"><?= formatRupiah($type['price_per_kg']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="bg-slate-900 text-slate-400 py-8 text-center text-sm border-t border-slate-800">
        <p>© 2026 BOWO (Bank of Waste Online). All rights reserved.</p>
    </footer>
</body>
</html>
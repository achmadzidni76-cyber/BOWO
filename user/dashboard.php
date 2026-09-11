<?php
require_once '../config/functions.php';
checkLogin();

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT IFNULL(SUM(weight), 0) FROM deposits WHERE user_id = ? AND status = 'diverifikasi'");
$stmt->execute([$user_id]);
$total_weight = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM deposits WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_deposits = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT * FROM targets WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$user_id]);
$target = $stmt->fetch();
$target_weight = ($target && $target['target_weight'] > 0) ? (float)$target['target_weight'] : 10.0;
$progress_pct = min(100, round(($total_weight / $target_weight) * 100));

$stmt = $pdo->prepare("SELECT d.*, w.name AS waste_name FROM deposits d JOIN waste_types w ON d.waste_type_id = w.id WHERE d.user_id = ? ORDER BY d.id DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_deposits = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Nasabah — BOWO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen flex">
    <aside class="w-64 bg-emerald-900 text-emerald-100 flex flex-col p-6 space-y-6">
        <a href="dashboard.php" class="flex items-center gap-3">
            <img src="../assets/images/bowo.png" alt="BOWO Logo" class="h-9 w-auto">
            <span class="text-2xl font-black text-white">BOWO</span>
        </a>
        <nav class="flex-1 space-y-2 text-sm font-medium">
            <a href="dashboard.php" class="block py-2.5 px-4 bg-emerald-800 text-white rounded-lg">Dashboard</a>
            <a href="setor.php" class="block py-2.5 px-4 hover:bg-emerald-800 rounded-lg transition">Setor Sampah</a>
            <a href="riwayat.php" class="block py-2.5 px-4 hover:bg-emerald-800 rounded-lg transition">Riwayat & Saldo</a>
            <a href="../leaderboard.php" class="block py-2.5 px-4 hover:bg-emerald-800 rounded-lg transition">Leaderboard</a>
            <a href="../logout.php" class="block py-2.5 px-4 text-red-300 hover:bg-red-900/50 rounded-lg transition">Logout</a>
        </nav>
    </aside>

    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold">Halo, <?= htmlspecialchars($user['name']) ?> 👋</h2>
            <span class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-xs font-semibold">Nasabah Aktif</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border">
                <div class="text-slate-500 text-sm">Saldo Saat Ini</div>
                <div class="text-2xl font-black text-emerald-600 mt-1"><?= formatRupiah($user['balance']) ?></div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border">
                <div class="text-slate-500 text-sm">Total Poin Kontribusi</div>
                <div class="text-2xl font-black text-amber-500 mt-1"><?= number_format($user['points']) ?> Pts</div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border">
                <div class="text-slate-500 text-sm">Sampah Terdaur Ulang</div>
                <div class="text-2xl font-black text-slate-800 mt-1"><?= number_format($total_weight, 1) ?> kg</div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border">
                <div class="text-slate-500 text-sm">Total Pengajuan</div>
                <div class="text-2xl font-black text-slate-800 mt-1"><?= $total_deposits ?> Kali</div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border mb-8">
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-bold text-slate-700">Target Setoran Bulan Ini</h3>
                <span class="text-sm text-slate-500"><?= number_format($total_weight, 1) ?> kg / <?= number_format($target_weight, 1) ?> kg</span>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-4 overflow-hidden">
                <div class="bg-emerald-500 h-4 rounded-full transition-all duration-500" style="width: <?= $progress_pct ?>%"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-6">
            <h3 class="font-bold text-lg text-slate-800 mb-4">Riwayat Setoran Terbaru</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 border-b">
                        <tr>
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">Jenis Sampah</th>
                            <th class="p-3">Berat</th>
                            <th class="p-3">Estimasi Nilai</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($recent_deposits as $dep): ?>
                            <tr>
                                <td class="p-3"><?= $dep['deposit_date'] ?></td>
                                <td class="p-3 font-medium"><?= htmlspecialchars($dep['waste_name']) ?></td>
                                <td class="p-3"><?= $dep['weight'] ?> kg</td>
                                <td class="p-3 font-semibold"><?= formatRupiah($dep['total_value']) ?></td>
                                <td class="p-3">
                                    <?php if($dep['status'] === 'diverifikasi'): ?>
                                        <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full text-xs font-bold">Diverifikasi</span>
                                    <?php elseif($dep['status'] === 'ditolak'): ?>
                                        <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-full text-xs font-bold">Ditolak</span>
                                    <?php else: ?>
                                        <span class="bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full text-xs font-bold">Menunggu</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recent_deposits)): ?>
                            <tr><td colspan="5" class="p-4 text-center text-slate-400">Belum ada riwayat setoran.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
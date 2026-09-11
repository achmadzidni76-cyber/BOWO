<?php
require_once '../config/functions.php';
checkLogin();

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT t.*, d.deposit_date 
                       FROM transactions t 
                       LEFT JOIN deposits d ON t.deposit_id = d.id 
                       WHERE t.user_id = ? ORDER BY t.id DESC");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat & Mutasi Saldo — BOWO</title>
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
            <a href="dashboard.php" class="block py-2.5 px-4 hover:bg-emerald-800 rounded-lg">Dashboard</a>
            <a href="setor.php" class="block py-2.5 px-4 hover:bg-emerald-800 rounded-lg">Setor Sampah</a>
            <a href="riwayat.php" class="block py-2.5 px-4 bg-emerald-800 text-white rounded-lg">Riwayat & Saldo</a>
            <a href="../leaderboard.php" class="block py-2.5 px-4 hover:bg-emerald-800 rounded-lg">Leaderboard</a>
            <a href="../logout.php" class="block py-2.5 px-4 text-red-300 hover:bg-red-900/50 rounded-lg">Logout</a>
        </nav>
    </aside>

    <main class="flex-1 p-8">
        <h2 class="text-2xl font-bold mb-6">Riwayat Mutasi Saldo</h2>
        <div class="bg-white rounded-2xl shadow-sm border p-6">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="p-3">Tanggal Transaksi</th>
                        <th class="p-3">Keterangan</th>
                        <th class="p-3">Tipe</th>
                        <th class="p-3">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td class="p-3 text-slate-500"><?= date('d M Y, H:i', strtotime($tx['created_at'])) ?></td>
                            <td class="p-3 font-medium"><?= htmlspecialchars($tx['description']) ?></td>
                            <td class="p-3">
                                <span class="bg-emerald-100 text-emerald-800 text-xs px-2.5 py-1 rounded-full font-bold">Kredit (Masuk)</span>
                            </td>
                            <td class="p-3 font-black text-emerald-600">+<?= formatRupiah($tx['amount']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($transactions)): ?>
                        <tr><td colspan="4" class="p-4 text-center text-slate-400">Belum ada catatan mutasi saldo.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
<?php
require_once '../config/functions.php';
checkAdmin();

$pending = $pdo->query("SELECT d.*, u.name as user_name, u.phone as user_phone, w.name as waste_name 
                        FROM deposits d 
                        JOIN users u ON d.user_id = u.id 
                        JOIN waste_types w ON d.waste_type_id = w.id 
                        WHERE d.status = 'menunggu' 
                        ORDER BY d.id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Verifikasi Admin — BOWO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen flex">
    <aside class="w-64 bg-slate-900 text-slate-200 flex flex-col p-6 space-y-6">
        <a href="dashboard.php" class="flex items-center gap-3">
            <img src="../assets/images/bowo.png" alt="BOWO Logo" class="h-9 w-auto">
            <span class="text-2xl font-black text-emerald-500">Admin</span>
        </a>
        <nav class="flex-1 space-y-2 text-sm font-medium">
            <a href="dashboard.php" class="block py-2.5 px-4 bg-emerald-600 text-white rounded-lg">Verifikasi Setoran</a>
            <a href="jenis_sampah.php" class="block py-2.5 px-4 hover:bg-slate-800 rounded-lg">Jenis & Harga Sampah</a>
            <a href="laporan.php" class="block py-2.5 px-4 hover:bg-slate-800 rounded-lg">Laporan Sistem</a>
            <a href="../logout.php" class="block py-2.5 px-4 text-red-400 hover:bg-slate-800 rounded-lg">Logout</a>
        </nav>
    </aside>

    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold">Verifikasi Setoran Sampah</h2>
                <p class="text-slate-500 text-sm">Kelola dan konfirmasi pengajuan setoran sampah dari nasabah.</p>
            </div>
            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1.5 rounded-full">
                <?= count($pending) ?> Pengajuan Menunggu
            </span>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b">
                        <tr>
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">Nasabah</th>
                            <th class="p-3">Jenis Sampah</th>
                            <th class="p-3">Berat</th>
                            <th class="p-3">Total Nilai</th>
                            <th class="p-3">Poin</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($pending as $p): ?>
                            <tr>
                                <td class="p-3 text-slate-500"><?= $p['deposit_date'] ?></td>
                                <td class="p-3">
                                    <div class="font-bold text-slate-800"><?= htmlspecialchars($p['user_name']) ?></div>
                                    <div class="text-xs text-slate-400"><?= htmlspecialchars($p['user_phone']) ?></div>
                                </td>
                                <td class="p-3 font-medium"><?= htmlspecialchars($p['waste_name']) ?></td>
                                <td class="p-3 font-semibold"><?= $p['weight'] ?> kg</td>
                                <td class="p-3 font-bold text-emerald-600"><?= formatRupiah($p['total_value']) ?></td>
                                <td class="p-3 font-bold text-amber-500"><?= $p['points'] ?> Pts</td>
                                <td class="p-3">
                                    <form action="verifikasi.php" method="POST" class="flex justify-center gap-2">
                                        <input type="hidden" name="deposit_id" value="<?= $p['id'] ?>">
                                        <button type="submit" name="action" value="verifikasi" onclick="return confirm('Verifikasi setoran ini?')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold">
                                            Setujui
                                        </button>
                                        <button type="submit" name="action" value="ditolak" onclick="return confirm('Tolak setoran ini?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold">
                                            Tolak
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($pending)): ?>
                            <tr>
                                <td colspan="7" class="p-6 text-center text-slate-400">Tidak ada pengajuan setoran yang menunggu verifikasi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
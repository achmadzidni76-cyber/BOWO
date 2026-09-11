<?php
require_once '../config/functions.php';
checkAdmin();

$total_volume = $pdo->query("SELECT IFNULL(SUM(weight), 0) FROM deposits WHERE status = 'diverifikasi'")->fetchColumn();
$total_cashout = $pdo->query("SELECT IFNULL(SUM(total_value), 0) FROM deposits WHERE status = 'diverifikasi'")->fetchColumn();
$total_points = $pdo->query("SELECT IFNULL(SUM(points), 0) FROM deposits WHERE status = 'diverifikasi'")->fetchColumn();

$reports = $pdo->query("SELECT d.*, u.name as user_name, w.name as waste_name 
                        FROM deposits d 
                        JOIN users u ON d.user_id = u.id 
                        JOIN waste_types w ON d.waste_type_id = w.id 
                        ORDER BY d.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi — BOWO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        @media print {
            aside, .no-print { display: none !important; }
            body { background: white; }
            main { padding: 0; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen flex">
    <aside class="w-64 bg-slate-900 text-slate-200 flex flex-col p-6 space-y-6 no-print">
        <a href="dashboard.php" class="flex items-center gap-3">
            <img src="../assets/images/bowo.png" alt="BOWO Logo" class="h-9 w-auto">
            <span class="text-2xl font-black text-emerald-500">Admin</span>
        </a>
        <nav class="flex-1 space-y-2 text-sm font-medium">
            <a href="dashboard.php" class="block py-2.5 px-4 hover:bg-slate-800 rounded-lg">Verifikasi Setoran</a>
            <a href="jenis_sampah.php" class="block py-2.5 px-4 hover:bg-slate-800 rounded-lg">Jenis & Harga Sampah</a>
            <a href="laporan.php" class="block py-2.5 px-4 bg-emerald-600 text-white rounded-lg">Laporan Sistem</a>
            <a href="../logout.php" class="block py-2.5 px-4 text-red-400 hover:bg-slate-800 rounded-lg">Logout</a>
        </nav>
    </aside>

    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-4">
                <img src="../assets/images/bowo.png" alt="BOWO Logo" class="h-12 w-auto">
                <div>
                    <h2 class="text-2xl font-bold">Laporan Rekapitulasi Sistem</h2>
                    <p class="text-slate-500 text-sm">Bank of Waste Online (BOWO)</p>
                </div>
            </div>
            <button onclick="window.print()" class="no-print bg-emerald-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-emerald-700 shadow-md">🖨️ Cetak Laporan</button>
        </div>

        <div class="grid grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-5 rounded-xl border">
                <div class="text-slate-500 text-xs">Total Volume Sampah</div>
                <div class="text-2xl font-black text-slate-800 mt-1"><?= number_format($total_volume, 1) ?> kg</div>
            </div>
            <div class="bg-white p-5 rounded-xl border">
                <div class="text-slate-500 text-xs">Total Nilai Transaksi</div>
                <div class="text-2xl font-black text-emerald-600 mt-1"><?= formatRupiah($total_cashout) ?></div>
            </div>
            <div class="bg-white p-5 rounded-xl border">
                <div class="text-slate-500 text-xs">Total Poin Disalurkan</div>
                <div class="text-2xl font-black text-amber-500 mt-1"><?= number_format($total_points) ?> Pts</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border p-6">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Nasabah</th>
                        <th class="p-3">Jenis Sampah</th>
                        <th class="p-3">Berat</th>
                        <th class="p-3">Total Nilai</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($reports as $r): ?>
                        <tr>
                            <td class="p-3"><?= $r['deposit_date'] ?></td>
                            <td class="p-3 font-semibold"><?= htmlspecialchars($r['user_name']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($r['waste_name']) ?></td>
                            <td class="p-3"><?= $r['weight'] ?> kg</td>
                            <td class="p-3 font-bold"><?= formatRupiah($r['total_value']) ?></td>
                            <td class="p-3 uppercase text-xs font-bold"><?= $r['status'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
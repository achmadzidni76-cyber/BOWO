<?php
require_once 'config/functions.php';

$stmt = $pdo->query("SELECT name, points, balance,
                      (SELECT IFNULL(SUM(weight), 0) FROM deposits WHERE user_id = users.id AND status = 'diverifikasi') as total_weight
                      FROM users WHERE role = 'nasabah' ORDER BY points DESC LIMIT 10");
$rankings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Leaderboard Eco-Hero — BOWO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-slate-50 min-h-screen text-slate-800">
    <div class="max-w-4xl mx-auto px-6 py-12">
        <div class="text-center mb-10">
            <a href="index.php" class="inline-flex items-center gap-3 mb-4">
                <img src="assets/images/bowo.png" alt="BOWO Logo" class="h-12 w-auto">
                <span class="text-3xl font-black text-emerald-600">BOWO</span>
            </a>
            <h1 class="text-3xl font-black text-slate-900">Leaderboard Eco-Hero</h1>
            <p class="text-slate-500 mt-1 text-sm">Apresiasi khusus nasabah teraktif yang menjaga bumi bersama BOWO.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-md border overflow-hidden">
            <div class="p-6 divide-y divide-slate-100">
                <?php foreach ($rankings as $index => $rank): ?>
                    <?php 
                        $pos = $index + 1; 
                        $badge = $pos === 1 ? '🥇' : ($pos === 2 ? '🥈' : ($pos === 3 ? '🥉' : '#' . $pos));
                        $bg = $pos === 1 ? 'bg-amber-50/60' : '';
                    ?>
                    <div class="flex items-center justify-between py-4 px-3 rounded-xl <?= $bg ?>">
                        <div class="flex items-center space-x-4">
                            <span class="text-2xl font-black w-10 text-center text-slate-700"><?= $badge ?></span>
                            <div>
                                <h3 class="font-bold text-slate-800 text-lg"><?= htmlspecialchars($rank['name']) ?></h3>
                                <p class="text-xs text-slate-500">Telah mendaur ulang <?= number_format($rank['total_weight'], 1) ?> kg sampah</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-emerald-600 font-extrabold text-xl"><?= number_format($rank['points']) ?></span>
                            <span class="text-xs text-slate-400 block font-semibold">Poin Kontribusi</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="index.php" class="text-emerald-600 font-bold hover:underline text-sm">← Kembali ke Landing Page</a>
        </div>
    </div>
</body>
</html>
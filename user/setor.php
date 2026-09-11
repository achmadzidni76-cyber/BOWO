<?php
require_once '../config/functions.php';
checkLogin();

$waste_types = $pdo->query("SELECT * FROM waste_types WHERE status = 'active'")->fetchAll();
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $waste_type_id = (int)$_POST['waste_type_id'];
    $weight = (float)$_POST['weight'];
    $deposit_date = $_POST['deposit_date'];
    $notes = sanitize($_POST['notes']);

    if ($weight <= 0) {
        $error = "Berat sampah harus lebih dari 0 kg.";
    } else {
        $stmt = $pdo->prepare("SELECT price_per_kg FROM waste_types WHERE id = ?");
        $stmt->execute([$waste_type_id]);
        $waste = $stmt->fetch();

        if ($waste) {
            $price_per_kg = $waste['price_per_kg'];
            $total_value = $weight * $price_per_kg;
            $points = ceil($weight * 10);

            $insert = $pdo->prepare("INSERT INTO deposits (user_id, waste_type_id, weight, price_per_kg, total_value, points, deposit_date, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'menunggu')");
            if ($insert->execute([$_SESSION['user_id'], $waste_type_id, $weight, $price_per_kg, $total_value, $points, $deposit_date, $notes])) {
                $success = "Pengajuan setoran sampah berhasil dikirim!";
            } else {
                $error = "Gagal mengirim pengajuan setoran.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Setor Sampah — BOWO</title>
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
            <a href="setor.php" class="block py-2.5 px-4 bg-emerald-800 text-white rounded-lg">Setor Sampah</a>
            <a href="riwayat.php" class="block py-2.5 px-4 hover:bg-emerald-800 rounded-lg">Riwayat & Saldo</a>
            <a href="../leaderboard.php" class="block py-2.5 px-4 hover:bg-emerald-800 rounded-lg">Leaderboard</a>
            <a href="../logout.php" class="block py-2.5 px-4 text-red-300 hover:bg-red-900/50 rounded-lg">Logout</a>
        </nav>
    </aside>

    <main class="flex-1 p-8">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-sm border">
            <h2 class="text-2xl font-bold mb-6 text-slate-800">Form Setoran Sampah</h2>

            <?php if ($success): ?>
                <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-lg mb-4 text-sm"><?= $success ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Pilih Jenis Sampah</label>
                    <select name="waste_type_id" id="waste_type_id" onchange="calculateValue()" required class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                        <?php foreach ($waste_types as $w): ?>
                            <option value="<?= $w['id'] ?>" data-price="<?= $w['price_per_kg'] ?>"><?= htmlspecialchars($w['name']) ?> (<?= formatRupiah($w['price_per_kg']) ?>/kg)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Berat (Kg)</label>
                    <input type="number" step="0.1" name="weight" id="weight" oninput="calculateValue()" required class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Tanggal Penyerahan</label>
                    <input type="date" name="deposit_date" value="<?= date('Y-m-d') ?>" required class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Catatan Tambahan</label>
                    <textarea name="notes" rows="2" class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100 text-sm">
                    <div class="text-slate-600">Estimasi Nilai Setoran: <span id="est_val" class="font-bold text-emerald-700 text-base">Rp0</span></div>
                    <div class="text-slate-600 mt-1">Estimasi Poin Diperoleh: <span id="est_pts" class="font-bold text-amber-600 text-base">0 Pts</span></div>
                </div>

                <div class="flex gap-4 pt-4">
                    <a href="dashboard.php" class="w-1/2 text-center py-2.5 border rounded-lg font-semibold text-slate-600 hover:bg-slate-50">Kembali</a>
                    <button type="submit" class="w-1/2 bg-emerald-600 text-white font-bold py-2.5 rounded-lg hover:bg-emerald-700">Kirim Setoran</button>
                </div>
            </form>
        </div>
    </main>

    <script>
    function calculateValue() {
        const select = document.getElementById('waste_type_id');
        const price = parseFloat(select.options[select.selectedIndex].getAttribute('data-price')) || 0;
        const weight = parseFloat(document.getElementById('weight').value) || 0;
        
        const total = price * weight;
        const points = Math.ceil(weight * 10);

        document.getElementById('est_val').innerText = 'Rp' + total.toLocaleString('id-ID');
        document.getElementById('est_pts').innerText = points + ' Pts';
    }
    </script>
</body>
</html>
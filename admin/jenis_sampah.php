<?php
require_once '../config/functions.php';
checkAdmin();

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $category = sanitize($_POST['category']);
    $price = (float)$_POST['price_per_kg'];
    $description = sanitize($_POST['description']);
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE waste_types SET name = ?, category = ?, price_per_kg = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $category, $price, $description, $id]);
        $msg = "Jenis sampah berhasil diperbarui!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO waste_types (name, category, price_per_kg, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $category, $price, $description]);
        $msg = "Jenis sampah baru berhasil ditambahkan!";
    }
}

if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM waste_types WHERE id = ?");
    $stmt->execute([$del_id]);
    header("Location: jenis_sampah.php");
    exit;
}

$types = $pdo->query("SELECT * FROM waste_types ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Jenis Sampah — Admin BOWO</title>
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
            <a href="dashboard.php" class="block py-2.5 px-4 hover:bg-slate-800 rounded-lg">Verifikasi Setoran</a>
            <a href="jenis_sampah.php" class="block py-2.5 px-4 bg-emerald-600 text-white rounded-lg">Jenis & Harga Sampah</a>
            <a href="laporan.php" class="block py-2.5 px-4 hover:bg-slate-800 rounded-lg">Laporan Sistem</a>
            <a href="../logout.php" class="block py-2.5 px-4 text-red-400 hover:bg-slate-800 rounded-lg">Logout</a>
        </nav>
    </aside>

    <main class="flex-1 p-8 grid md:grid-cols-3 gap-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border h-fit">
            <h3 class="font-bold text-lg mb-4 text-slate-800">Form Jenis Sampah</h3>
            <?php if ($msg): ?>
                <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-2 rounded-lg text-sm mb-4"><?= $msg ?></div>
            <?php endif; ?>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="id" id="edit_id">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Sampah</label>
                    <input type="text" name="name" id="edit_name" required class="mt-1 w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Kategori</label>
                    <input type="text" name="category" id="edit_category" required placeholder="Plastik, Kertas, Metal..." class="mt-1 w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Harga Per Kg (Rp)</label>
                    <input type="number" step="100" name="price_per_kg" id="edit_price" required class="mt-1 w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Deskripsi</label>
                    <textarea name="description" id="edit_description" rows="2" class="mt-1 w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-lg transition">Simpan Data</button>
            </form>
        </div>

        <div class="md:col-span-2 bg-white p-6 rounded-2xl shadow-sm border">
            <h3 class="font-bold text-lg mb-4 text-slate-800">Daftar Jenis & Harga Sampah</h3>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Harga/Kg</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($types as $t): ?>
                        <tr>
                            <td class="p-3 font-semibold"><?= htmlspecialchars($t['name']) ?></td>
                            <td class="p-3"><span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-xs"><?= htmlspecialchars($t['category']) ?></span></td>
                            <td class="p-3 font-bold text-emerald-600"><?= formatRupiah($t['price_per_kg']) ?></td>
                            <td class="p-3 text-right space-x-2">
                                <button onclick='fillForm(<?= json_encode($t) ?>)' class="text-emerald-600 hover:underline font-semibold text-xs">Edit</button>
                                <a href="jenis_sampah.php?delete=<?= $t['id'] ?>" onclick="return confirm('Hapus jenis sampah ini?')" class="text-red-500 hover:underline font-semibold text-xs">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
    function fillForm(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_name').value = data.name;
        document.getElementById('edit_category').value = data.category;
        document.getElementById('edit_price').value = data.price_per_kg;
        document.getElementById('edit_description').value = data.description;
    }
    </script>
</body>
</html>
<?php
require_once '../config/functions.php';
checkAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['deposit_id'])) {
    $deposit_id = (int)$_POST['deposit_id'];
    $action = $_POST['action'];

    $stmt = $pdo->prepare("SELECT * FROM deposits WHERE id = ? AND status = 'menunggu'");
    $stmt->execute([$deposit_id]);
    $deposit = $stmt->fetch();

    if ($deposit) {
        if ($action === 'verifikasi') {
            $pdo->beginTransaction();
            try {
                // Update status setoran
                $up = $pdo->prepare("UPDATE deposits SET status = 'diverifikasi', verified_by = ?, verified_at = NOW() WHERE id = ?");
                $up->execute([$_SESSION['user_id'], $deposit_id]);

                // Tambah saldo & poin nasabah
                $up_user = $pdo->prepare("UPDATE users SET balance = balance + ?, points = points + ? WHERE id = ?");
                $up_user->execute([$deposit['total_value'], $deposit['points'], $deposit['user_id']]);

                // Catat mutasi saldo di transaksi
                $ins_tx = $pdo->prepare("INSERT INTO transactions (user_id, deposit_id, type, amount, description) VALUES (?, ?, 'setoran_masuk', ?, ?)");
                $ins_tx->execute([
                    $deposit['user_id'],
                    $deposit_id,
                    $deposit['total_value'],
                    "Hasil setoran sampah " . $deposit['weight'] . " kg"
                ]);

                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
            }
        } elseif ($action === 'ditolak') {
            $up = $pdo->prepare("UPDATE deposits SET status = 'ditolak', verified_by = ?, verified_at = NOW() WHERE id = ?");
            $up->execute([$_SESSION['user_id'], $deposit_id]);
        }
    }
}

header("Location: dashboard.php");
exit;
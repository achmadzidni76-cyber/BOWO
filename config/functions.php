<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php';

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit;
    }
}

function checkAdmin() {
    checkLogin();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: ../user/dashboard.php");
        exit;
    }
}

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}
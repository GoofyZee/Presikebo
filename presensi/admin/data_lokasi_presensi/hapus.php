<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../config.php');

// Validasi ID
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: lokasi_presensi.php?status=invalid_id");
    exit;
}

// Pastikan data dengan ID tersebut ada
$stmt = $connection->prepare("SELECT id FROM lokasi_presensi WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$exists = $result->num_rows > 0;
$stmt->close();

if (!$exists) {
    header("Location: lokasi_presensi.php?status=not_found&action=delete");
    exit;
}

// Lakukan penghapusan
$stmt = $connection->prepare("DELETE FROM lokasi_presensi WHERE id = ?");
$stmt->bind_param("i", $id);
$exec = $stmt->execute();
$stmt->close();

if ($exec) {
    header("Location: lokasi_presensi.php?status=success&action=delete");
} else {
    header("Location: lokasi_presensi.php?status=fail&action=delete");
}
exit;

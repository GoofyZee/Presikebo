<?php
session_start();

header('Content-Type: application/json');

// Cek login & role admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
    exit;
}

require_once('../../config.php');

$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan']);
    exit;
}

// Mulai transaksi
$connection->begin_transaction();

try {
    // Ambil data pegawai dulu untuk cek eksistensi dan ambil foto
    $stmt_foto = $connection->prepare("SELECT foto FROM pegawai WHERE id = ?");
    if (!$stmt_foto) throw new Exception("Prepare gagal (foto)");
    $stmt_foto->bind_param("i", $id);
    $stmt_foto->execute();
    $result_foto = $stmt_foto->get_result();
    $foto_data = $result_foto->fetch_assoc();
    $stmt_foto->close();

    if (!$foto_data) {
        throw new Exception("Data pegawai tidak ditemukan");
    }

    $foto = $foto_data['foto'] ?? null;

    // Hapus dari tabel users
    $stmt_user = $connection->prepare("DELETE FROM users WHERE id_pegawai = ?");
    if (!$stmt_user) throw new Exception("Prepare gagal (users)");
    $stmt_user->bind_param("i", $id);
    $stmt_user->execute();
    $stmt_user->close();

    // Hapus dari tabel pegawai
    $stmt_pegawai = $connection->prepare("DELETE FROM pegawai WHERE id = ?");
    if (!$stmt_pegawai) throw new Exception("Prepare gagal (pegawai)");
    $stmt_pegawai->bind_param("i", $id);
    $stmt_pegawai->execute();
    $stmt_pegawai->close();

    // Hapus file foto jika ada
    if (!empty($foto) && file_exists("../../uploads/foto/" . $foto)) {
        unlink("../../uploads/foto/" . $foto);
    }

    // Commit transaksi
    $connection->commit();
    echo json_encode(['status' => 'success', 'message' => 'Data berhasil dihapus']);
} catch (Exception $e) {
    $connection->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
}

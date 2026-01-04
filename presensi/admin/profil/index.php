<?php
session_start();

if (!isset($_SESSION['login'], $_SESSION['user'], $_SESSION['role'])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
}

require_once('../../config.php');
$judul = "Profil Saya";
include('../layout/header.php');

if (isset($_GET['pesan']) && $_GET['pesan'] === 'update_berhasil') {
    echo "<script>alert('Profil berhasil diperbarui!');</script>";
}

// Ambil data user dari session
$user = $_SESSION['user'];

// Jika butuh refresh data dari DB (misal setelah update), bisa tambahkan query ulang di sini
// $user = ambil data dari DB pakai $_SESSION['id'] atau $_SESSION['user']['id']

$foto = $user['foto'] ?: 'avatar.jpg';
?>

<div class="main">
  <main class="content">
    <div class="container-fluid p-4">
      <h1 class="h3 mb-4">Profil Saya</h1>
      <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body text-center">
          <img src="/presensi/assets/img/avatars/<?= htmlspecialchars($foto) ?>" alt="Foto Profil" class="rounded-circle mb-3" width="120">
          <h4><?= htmlspecialchars($user['nama']) ?></h4>
          <p class="text-muted"><?= htmlspecialchars($_SESSION['role']) ?></p>

          <table class="table mt-4 text-start">
            <tr><th>NIP</th><td>: <?= htmlspecialchars($user['nip'] ?? '-') ?></td></tr>
            <tr><th>Nama</th><td>: <?= htmlspecialchars($user['nama'] ?? '-') ?></td></tr>
            <tr><th>Jenis Kelamin</th><td>: <?= htmlspecialchars($user['jenis_kelamin'] ?? '-') ?></td></tr>
            <tr><th>Alamat</th><td>: <?= htmlspecialchars($user['alamat'] ?? '-') ?></td></tr>
            <tr><th>No. Handphone</th><td>: <?= htmlspecialchars($user['no_handphone'] ?? '-') ?></td></tr>
            <tr><th>Jabatan</th><td>: <?= htmlspecialchars($user['jabatan'] ?? '-') ?></td></tr>
            <tr><th>Lokasi Presensi</th><td>: <?= htmlspecialchars($user['lokasi_presensi'] ?? '-') ?></td></tr>
          </table>

          <a href="/presensi/admin/profil/edit.php" class="btn btn-primary mt-3">Edit Profil</a>
        </div>
      </div>
    </div>
  </main>
</div>

<?php include('../layout/footer.php'); ?>

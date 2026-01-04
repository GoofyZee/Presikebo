<?php
session_start();

if (!isset($_SESSION['login'], $_SESSION['user'], $_SESSION['role'])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
}

require_once('../../config.php');
$judul = "Profil Saya";

include('../layout/header.php');

// Tampilkan notifikasi jika ada pesan dari redirect edit
if (isset($_GET['pesan']) && $_GET['pesan'] === 'update_berhasil') {
    echo "<script>alert('Profil berhasil diperbarui!');</script>";
}

// Ambil data dari session
$user = $_SESSION['user'];
$nama = $user['nama'] ?? '-';
$role = $_SESSION['role'] ?? '-';
$jenis_kelamin = $user['jenis_kelamin'] ?? 'Tidak Diketahui';
$alamat = $user['alamat'] ?? 'Tidak Diketahui';
$no_hp = $user['no_handphone'] ?? 'Tidak Diketahui'; // FIXED
?>


<div class="main">
  <main class="content">
    <div class="container-fluid p-4">
      <h1 class="h3 mb-4">Profil Saya</h1>
      <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body text-center">
          <img src="/presensi/assets/img/avatars/avatar.jpg" alt="Avatar" class="rounded-circle mb-3" width="120">
          <h4><?= htmlspecialchars($nama) ?></h4>
          <p class="text-muted"><?= htmlspecialchars($role) ?></p>

          <table class="table mt-4 text-start">
            <tr>
              <th width="30%">Nama</th>
              <td>: <?= htmlspecialchars($nama) ?></td>
            </tr>
            <tr>
              <th>Jenis Kelamin</th>
              <td>: <?= htmlspecialchars($jenis_kelamin) ?></td>
            </tr>
            <tr>
              <th>Alamat</th>
              <td>: <?= htmlspecialchars($alamat) ?></td>
            </tr>
            <tr>
              <th>No. Handphone</th>
              <td>: <?= htmlspecialchars($no_hp) ?></td>
            </tr>
          </table>
          
          <!-- Tombol Edit Profil -->
          <a href="/presensi/admin/profil/edit.php" class="btn btn-primary mt-3">Edit Profil</a>
        </div>
      </div>
    </div>
  </main>
</div>

<?php include('../layout/footer.php'); ?>

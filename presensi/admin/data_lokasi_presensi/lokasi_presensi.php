<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

$judul = "Lokasi Presensi";
require_once('../../config.php');
include('../layout/header.php');

// Ambil data lokasi presensi
$result = mysqli_query($connection, "SELECT * FROM lokasi_presensi ORDER BY id DESC");
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_GET['status']) && isset($_GET['action'])): ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const status = <?= json_encode($_GET['status']) ?>;
    const action = <?= json_encode($_GET['action']) ?>;

    let title = status === 'success' ? 'Berhasil!' : 'Gagal!';
    let icon = status === 'success' ? 'success' : 'error';

    let actionText = {
      add: 'menambahkan data',
      edit: 'mengubah data',
      delete: 'menghapus data'
    }[action] || 'melakukan aksi';

    Swal.fire({
      icon: icon,
      title: title,
      text: `Sukses ${actionText}.`,
      timer: 2000,
      showConfirmButton: false
    });
  });
</script>
<?php endif; ?>

<div class="main">
  <main class="content">
    <div class="container-fluid p-4">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="m-0">Data Lokasi Presensi</h3>
          <a href="tambah.php" class="btn btn-sm btn-primary">
            <i class="bi bi-plus"></i> Tambah Lokasi
          </a>
        </div>

        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
              <thead class="table-dark text-center">
                <tr>
                  <th>No</th>
                  <th>Nama Lokasi</th>
                  <th>Alamat</th>
                  <th>Tipe Lokasi</th>
                  <th>Latitude / Longitude</th>
                  <th>Radius</th>
                  <th>Zona Waktu</th>
                  <th>Jam Masuk</th>
                  <th>Jam Pulang</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (mysqli_num_rows($result) === 0): ?>
                  <tr>
                    <td colspan="10" class="text-center">Data Kosong</td>
                  </tr>
                <?php else: ?>
                  <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                      <td class="text-center"><?= $no++ ?></td>
                      <td><?= htmlspecialchars($row['nama_lokasi']) ?></td>
                      <td><?= htmlspecialchars($row['alamat_lokasi']) ?></td>
                      <td class="text-center"><?= htmlspecialchars($row['tipe_lokasi']) ?></td>
                      <td class="text-center"><?= htmlspecialchars($row['latitude']) ?> / <?= htmlspecialchars($row['longitude']) ?></td>
                      <td class="text-center"><?= htmlspecialchars($row['radius']) ?> m</td>
                      <td class="text-center"><?= htmlspecialchars($row['zona_waktu']) ?></td>
                      <td class="text-center"><?= htmlspecialchars($row['jam_masuk']) ?></td>
                      <td class="text-center"><?= htmlspecialchars($row['jam_pulang']) ?></td>
                      <td class="text-center">
                        <a href="<?= base_url('admin/data_lokasi_presensi/detail.php?id=' . $row['id']) ?>" class="btn btn-info btn-sm">Detail</a>
                        <a href="<?= base_url('admin/data_lokasi_presensi/edit.php?id=' . $row['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="<?= base_url('admin/data_lokasi_presensi/hapus.php?id=' . $row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin hapus?')">Hapus</a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </main>
</div>

<?php include('../layout/footer.php'); ?>

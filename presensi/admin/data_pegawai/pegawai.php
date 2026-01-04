<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

$judul = "Data Pegawai";
require_once('../../config.php');
include('../layout/header.php');

if (!$connection) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$sql = "SELECT 
            pegawai.id AS id_pegawai, 
            pegawai.nama, 
            pegawai.jabatan,
            pegawai.lokasi_presensi,
            users.role 
        FROM pegawai 
        LEFT JOIN users ON pegawai.id = users.id_pegawai";

$result = mysqli_query($connection, $sql);
if (!$result) {
    die("Query error: " . mysqli_error($connection));
}

$jumlah = mysqli_num_rows($result);
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_GET['status']) && isset($_GET['action'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const status = <?= json_encode($_GET['status']) ?>;
    const action = <?= json_encode($_GET['action']) ?>;
    let title = (status === 'success') ? 'Berhasil!' : 'Gagal!';
    let icon = (status === 'success') ? 'success' : 'error';
    let actionText = {
        'add': 'menambahkan data',
        'edit': 'mengubah data',
        'delete': 'menghapus data'
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
          <h3 class="m-0">Data Pegawai</h3>
          <a href="tambah.php" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah</span>
          </a>
        </div>
        <div class="card-body">
  <?php if ($jumlah === 0): ?>
    <div class="alert alert-warning">
      <strong>Data masih kosong</strong>, silakan tambahkan data terlebih dahulu.
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark text-center">
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Jabatan</th>
            <th>Lokasi Presensi</th>
            <th>Role</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
          <tr id="row-<?= $row['id_pegawai'] ?>">
            <td class="text-center"><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['jabatan']) ?></td>
            <td><?= htmlspecialchars($row['lokasi_presensi']) ?></td>
            <td class="text-center"><?= htmlspecialchars($row['role'] ?? '-') ?></td>
            <td class="text-center">
              <a href="detail.php?id=<?= urlencode($row['id_pegawai']) ?>" class="btn btn-info btn-sm">Detail</a>
              <a href="edit.php?id=<?= urlencode($row['id_pegawai']) ?>" class="btn btn-warning btn-sm">Edit</a>
              <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $row['id_pegawai'] ?>">Hapus</button>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
      </div>
    </div>
  </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const buttons = document.querySelectorAll('.btn-delete');

  buttons.forEach(button => {
    button.addEventListener('click', function() {
      const id = this.getAttribute('data-id');

      Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data pegawai yang dihapus tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('hapus.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id=' + encodeURIComponent(id)
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
              });
              const row = document.getElementById('row-' + id);
              if (row) row.remove();
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message
              });
            }
          })
          .catch(() => {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: 'Gagal menghubungi server.'
            });
          });
        }
      });
    });
  });
});
</script>

<?php include('../layout/footer.php'); ?>
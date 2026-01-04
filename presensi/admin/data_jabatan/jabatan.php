<?php
session_start();

// Cek login & role admin
if (!isset($_SESSION['login'])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
} elseif ($_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

$judul = "Data Jabatan";
require_once('../../config.php');
include('../layout/header.php');

// Ambil data jabatan
$result = mysqli_query($connection, "SELECT * FROM jabatan ORDER BY id DESC");
$jumlah = mysqli_num_rows($result);
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_GET['status']) && isset($_GET['action'])): ?>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const status = <?= json_encode($_GET['status']) ?>;
    const action = <?= json_encode($_GET['action']) ?>;
    
    let icon = status === 'success' ? 'success' : 'error';
    let title = status === 'success' ? 'Berhasil!' : 'Gagal!';
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
          <h5 class="m-0">Daftar Jabatan</h5>
          <a href="tambah.php" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah</span>
          </a>
        </div>

        <div class="card-body">
          <?php if ($jumlah === 0): ?>
            <div class="alert alert-warning text-center mb-0">
              <strong>Data masih kosong</strong>, silakan tambahkan data terlebih dahulu.
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark text-center">
                  <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Jabatan</th>
                    <th style="width: 160px;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $no = 1; while ($jabatan = mysqli_fetch_assoc($result)) : ?>
                    <tr id="row-<?= $jabatan['id'] ?>">
                      <td class="text-center"><?= $no++ ?></td>
                      <td><?= htmlspecialchars($jabatan['jabatan']) ?></td>
                      <td class="text-center">
                        <a href="edit.php?id=<?= $jabatan['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $jabatan['id'] ?>">Hapus</button>
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
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-delete').forEach(button => {
      button.addEventListener('click', () => {
        const id = button.dataset.id;

        Swal.fire({
          title: 'Yakin ingin menghapus?',
          text: "Data jabatan yang dihapus tidak bisa dikembalikan!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Ya, hapus!',
          cancelButtonText: 'Batal'
        }).then(result => {
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
                  timer: 1500,
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
                title: 'Terjadi Kesalahan',
                text: 'Tidak dapat terhubung ke server.'
              });
            });
          }
        });
      });
    });
  });
</script>

<?php include('../layout/footer.php'); ?>

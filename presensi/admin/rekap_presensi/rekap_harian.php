<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../config.php');
include('../layout/header.php');

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

$stmt = $connection->prepare("SELECT p.*, u.nama, u.nip 
    FROM presensi p 
    JOIN pegawai u ON p.id_pegawai = u.id 
    WHERE p.tanggal_masuk = ? 
    ORDER BY p.jam_masuk ASC");
$stmt->bind_param('s', $tanggal);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container py-4">
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Rekap Presensi Harian (<?= date('d-m-Y', strtotime($tanggal)) ?>)</h5>
    </div>
    <div class="card-body">
      <form class="row g-3 mb-3" method="get">
        <div class="col-md-4">
          <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>" class="form-control" required>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>NIP</th>
              <th>Jam Masuk</th>
              <th>Jam Keluar</th>
              <th>Selfie Masuk</th>
              <th>Selfie Keluar</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            while ($row = $result->fetch_assoc()):
              $masuk = $row['jam_masuk'] ?? '-';
              $keluar = $row['jam_keluar'] ?? '-';
              $foto_masuk = $row['foto_masuk'] ?? '';
              $foto_keluar = $row['foto_keluar'] ?? '';
            ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['nip']) ?></td>
                <td><?= $masuk ?></td>
                <td><?= $keluar ?></td>
                <td>
                  <?php if ($foto_masuk && file_exists("../../uploads/presensi/$foto_masuk")): ?>
                    <img src="../../uploads/presensi/<?= $foto_masuk ?>" width="80">
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($foto_keluar && file_exists("../../uploads/presensi/$foto_keluar")): ?>
                    <img src="../../uploads/presensi/<?= $foto_keluar ?>" width="80">
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include('../layout/footer.php'); ?>

<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../config.php');
include('../layout/header.php');

// Filter
$bulan = $_GET['bulan'] ?? date('Y-m');
$id_pegawai = $_GET['pegawai'] ?? '';

// Ambil daftar pegawai
$pegawai_q = mysqli_query($connection, "SELECT id, nama FROM pegawai ORDER BY nama ASC");

// Ambil data presensi
$where = "DATE_FORMAT(tanggal_masuk, '%Y-%m') = ?";
$params = [$bulan];
$types = 's';

if ($id_pegawai) {
    $where .= " AND id_pegawai = ?";
    $params[] = $id_pegawai;
    $types .= 'i';
}

$sql = "SELECT p.*, u.nama, u.nip FROM presensi p
        JOIN pegawai u ON p.id_pegawai = u.id
        WHERE $where ORDER BY tanggal_masuk DESC";

$stmt = $connection->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container py-4">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h5 class="mb-0">Rekap Presensi Bulanan</h5>
      <a href="export_rekap_bulanan_excel.php?bulan=<?= $bulan ?>&pegawai=<?= $id_pegawai ?>" class="btn btn-sm btn-success">📥 Export Excel</a>
    </div>
    <div class="card-body">
      <form class="row g-2 mb-3" method="get">
        <div class="col-md-4">
          <label>Bulan:</label>
          <input type="month" name="bulan" value="<?= htmlspecialchars($bulan) ?>" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>Pegawai:</label>
          <select name="pegawai" class="form-select">
            <option value="">-- Semua Pegawai --</option>
            <?php while ($pg = mysqli_fetch_assoc($pegawai_q)): ?>
              <option value="<?= $pg['id'] ?>" <?= $id_pegawai == $pg['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($pg['nama']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-2 d-grid align-content-end">
          <button class="btn btn-primary">Tampilkan</button>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Tanggal</th>
              <th>Jam Masuk</th>
              <th>Jam Keluar</th>
              <th>Telat</th>
              <th>Total Jam</th>
              <th>Selfie</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            while ($row = $result->fetch_assoc()):
                $masuk = $row['jam_masuk'];
                $keluar = $row['jam_keluar'];
                $foto = $row['foto_masuk'];
                $total = '-';

                // Hitung telat dan total jam
                $telat = strtotime($masuk) - strtotime('07:30:00');
                $telat_str = $telat > 0 ? floor($telat / 3600) . ' jam ' . floor(($telat % 3600) / 60) . ' mnt' : '-';

                if ($masuk && $keluar && $keluar != '00:00:00') {
                    $selisih = strtotime($keluar) - strtotime($masuk);
                    $total = floor($selisih / 3600) . ' jam ' . floor(($selisih % 3600) / 60) . ' mnt';
                }
            ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= date('d-m-Y', strtotime($row['tanggal_masuk'])) ?></td>
                <td><?= $masuk ?: '-' ?></td>
                <td><?= $keluar ?: '-' ?></td>
                <td><?= $telat_str ?></td>
                <td><?= $total ?></td>
                <td>
                  <?php if ($foto && file_exists("../../uploads/presensi/$foto")): ?>
                    <img src="../../uploads/presensi/<?= $foto ?>" width="80">
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

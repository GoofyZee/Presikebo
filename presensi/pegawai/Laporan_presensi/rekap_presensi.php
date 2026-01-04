<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'pegawai') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../config.php');
include('../layout/header.php');

$id_pegawai = $_SESSION['id'] ?? null;
if (!$id_pegawai) {
    die("ID pegawai tidak ditemukan di sesi.");
}

$mulai = $_GET['mulai'] ?? '';
$akhir = $_GET['akhir'] ?? '';

// Ambil data presensi berdasarkan id_pegawai
$query = "SELECT tanggal_masuk, jam_masuk, tanggal_keluar, jam_keluar FROM presensi WHERE id_pegawai = ?";
$params = [$id_pegawai];
$types = 's';

if ($mulai && $akhir) {
    $query .= " AND tanggal_masuk BETWEEN ? AND ?";
    $params[] = $mulai;
    $params[] = $akhir;
    $types .= 'ss';
}

$query .= " ORDER BY tanggal_masuk DESC";

$stmt = $connection->prepare($query);
if (!$stmt) {
    die("Query gagal: " . $connection->error);
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container py-4">
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Rekap Presensi</h5>
    </div>
    <div class="card-body">

      <form method="get" class="row g-2 mb-3">
        <div class="col-md-4">
          <input type="date" name="mulai" value="<?= htmlspecialchars($mulai) ?>" class="form-control" required>
        </div>
        <div class="col-md-4">
          <input type="date" name="akhir" value="<?= htmlspecialchars($akhir) ?>" class="form-control" required>
        </div>
        <div class="col-md-4">
          <button class="btn btn-primary w-100">Tampilkan</button>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-bordered table-hover text-center">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>Tanggal</th>
              <th>Jam Masuk</th>
              <th>Jam Keluar</th>
              <th>Total Jam</th>
              <th>Terlambat</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            while ($row = $result->fetch_assoc()):
                $jamMasuk = $row['jam_masuk'];
                $jamKeluar = $row['jam_keluar'];

                // Hitung total jam
                if ($jamMasuk && $jamKeluar && $jamKeluar !== '00:00:00') {
                    $selisih = strtotime($jamKeluar) - strtotime($jamMasuk);
                    $totalStr = floor($selisih / 3600) . ' Jam ' . floor(($selisih % 3600) / 60) . ' Menit';
                } else {
                    $totalStr = '0 Jam 0 Menit';
                }

                // Hitung keterlambatan
                $terlambat = strtotime($jamMasuk) - strtotime('07:30:00');
                $terlambatStr = $terlambat > 0
                    ? floor($terlambat / 3600) . ' Jam ' . floor(($terlambat % 3600) / 60) . ' Menit'
                    : '0 Jam 0 Menit';
            ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= date('d F Y', strtotime($row['tanggal_masuk'])) ?></td>
              <td><?= $jamMasuk ?: '-' ?></td>
              <td><?= $jamKeluar ?: '-' ?></td>
              <td><?= $totalStr ?></td>
              <td><?= $terlambatStr ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <a href="export_excel.php?mulai=<?= urlencode($mulai) ?>&akhir=<?= urlencode($akhir) ?>" class="btn btn-success mt-3">
        📥 Export Excel
      </a>

    </div>
  </div>
</div>

<?php include('../layout/footer.php'); ?>

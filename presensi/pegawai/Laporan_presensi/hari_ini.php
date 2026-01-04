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

$today = date('Y-m-d');

$stmt = $connection->prepare("SELECT tanggal_masuk, jam_masuk, tanggal_keluar, jam_keluar FROM presensi WHERE id_pegawai = ? AND tanggal_masuk = ?");
$stmt->bind_param('ss', $id_pegawai, $today);
$stmt->execute();
$result = $stmt->get_result();

$data = $result->fetch_assoc();
?>

<div class="container py-4">
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Presensi Hari Ini - <?= date('d F Y', strtotime($today)) ?></h5>
    </div>
    <div class="card-body">
      <?php if ($data): ?>
        <table class="table table-bordered text-center">
          <thead class="table-light">
            <tr>
              <th>Jam Masuk</th>
              <th>Jam Keluar</th>
              <th>Total Jam</th>
              <th>Terlambat</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $jamMasuk = $data['jam_masuk'];
              $jamKeluar = $data['jam_keluar'];

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
              <td><?= $jamMasuk ?: '-' ?></td>
              <td><?= $jamKeluar ?: '-' ?></td>
              <td><?= $totalStr ?></td>
              <td><?= $terlambatStr ?></td>
            </tr>
          </tbody>
        </table>
      <?php else: ?>
        <div class="alert alert-warning text-center">Belum ada data presensi untuk hari ini.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include('../layout/footer.php'); ?>

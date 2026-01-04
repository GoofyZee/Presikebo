<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../config.php');

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: lokasi_presensi.php");
    exit;
}

$stmt = $connection->prepare("SELECT * FROM lokasi_presensi WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: lokasi_presensi.php");
    exit;
}

include('../layout/header.php');
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="container mt-4">
  <h3>Detail Lokasi Presensi</h3>

  <div class="row">
    <!-- Kolom Kiri: Tabel Detail -->
    <div class="col-md-6">
      <table class="table table-bordered">
        <tr><th>Nama Lokasi</th><td><?= htmlspecialchars($data['nama_lokasi']) ?></td></tr>
        <tr><th>Alamat</th><td><?= htmlspecialchars($data['alamat_lokasi']) ?></td></tr>
        <tr><th>Tipe Lokasi</th><td><?= htmlspecialchars($data['tipe_lokasi']) ?></td></tr>
        <tr><th>Latitude</th><td><?= htmlspecialchars($data['latitude']) ?></td></tr>
        <tr><th>Longtitude</th><td><?= htmlspecialchars($data['longtitude']) ?></td></tr>
        <tr><th>Radius</th><td><?= htmlspecialchars($data['radius']) ?></td></tr>
        <tr><th>Zona Waktu</th><td><?= htmlspecialchars($data['zona_waktu']) ?></td></tr>
        <tr><th>Jam Masuk</th><td><?= htmlspecialchars($data['jam_masuk']) ?></td></tr>
        <tr><th>Jam Pulang</th><td><?= htmlspecialchars($data['jam_pulang']) ?></td></tr>
      </table>
      <a href="lokasi_presensi.php" class="btn btn-secondary mt-2">Kembali</a>
    </div>

    <!-- Kolom Kanan: Peta -->
    <div class="col-md-6">
      <div id="map" style="height: 450px;"></div>
    </div>
  </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  var map = L.map('map').setView([<?= $data['latitude'] ?>, <?= $data['longtitude'] ?>], 16);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  L.marker([<?= $data['latitude'] ?>, <?= $data['longtitude'] ?>])
    .addTo(map)
    .bindPopup('<?= addslashes($data['nama_lokasi']) ?>')
    .openPopup();
</script>

<?php include('../layout/footer.php'); ?>

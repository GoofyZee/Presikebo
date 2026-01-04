<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../config.php');
$error = ''; // inisialisasi

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil & bersihkan input
    $nama_lokasi = trim($_POST['nama_lokasi']);
    $alamat_lokasi = trim($_POST['alamat_lokasi']);
    $tipe_lokasi = trim($_POST['tipe_lokasi']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']); // <- diperbaiki dari "longtitude"
    $radius = isset($_POST['radius']) && is_numeric($_POST['radius']) ? (int)$_POST['radius'] : null;
    $zona_waktu = trim($_POST['zona_waktu']);
    $jam_masuk = $_POST['jam_masuk'];
    $jam_pulang = $_POST['jam_pulang'];

    // Validasi dasar
    if ($nama_lokasi === '') {
        $error = "Nama Lokasi wajib diisi.";
    } elseif ($radius === null || $radius <= 0) {
        $error = "Radius harus berupa angka lebih dari 0.";
    } elseif (!$jam_masuk || !$jam_pulang) {
        $error = "Jam masuk dan jam pulang wajib diisi.";
    } elseif (strtotime($jam_masuk) >= strtotime($jam_pulang)) {
        $error = "Jam pulang harus lebih lambat dari jam masuk.";
    } else {
        // Simpan ke DB
        $stmt = $connection->prepare("INSERT INTO lokasi_presensi (nama_lokasi, alamat_lokasi, tipe_lokasi, latitude, longitude, radius, zona_waktu, jam_masuk, jam_pulang) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssisss", $nama_lokasi, $alamat_lokasi, $tipe_lokasi, $latitude, $longitude, $radius, $zona_waktu, $jam_masuk, $jam_pulang);
        $exec = $stmt->execute();
        $stmt->close();

        if ($exec) {
            header("Location: lokasi_presensi.php?status=success&action=add");
            exit;
        } else {
            $error = "Gagal menambahkan data.";
        }
    }
}

include('../layout/header.php');
?>

<div class="container mt-4">
  <h3>Tambah Lokasi Presensi</h3>

  <?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="mb-3">
      <label>Nama Lokasi</label>
      <input type="text" name="nama_lokasi" class="form-control" required value="<?= $_POST['nama_lokasi'] ?? '' ?>">
    </div>
    <div class="mb-3">
      <label>Alamat Lokasi</label>
      <input type="text" name="alamat_lokasi" class="form-control" value="<?= $_POST['alamat_lokasi'] ?? '' ?>">
    </div>
    <div class="mb-3">
      <label>Tipe Lokasi</label>
      <input type="text" name="tipe_lokasi" class="form-control" value="<?= $_POST['tipe_lokasi'] ?? '' ?>">
    </div>
    <div class="mb-3">
      <label>Latitude</label>
      <input type="text" name="latitude" class="form-control" value="<?= $_POST['latitude'] ?? '' ?>">
    </div>
    <div class="mb-3">
      <label>Longitude</label> <!-- Sudah dibetulkan -->
      <input type="text" name="longitude" class="form-control" value="<?= $_POST['longitude'] ?? '' ?>">
    </div>
    <div class="mb-3">
      <label>Radius (meter)</label>
      <input type="number" name="radius" class="form-control" value="<?= $_POST['radius'] ?? '' ?>">
    </div>
    <div class="mb-3">
      <label>Zona Waktu (misal: WIB)</label>
      <input type="text" name="zona_waktu" maxlength="4" class="form-control" value="<?= $_POST['zona_waktu'] ?? '' ?>">
    </div>
    <div class="mb-3">
      <label>Jam Masuk</label>
      <input type="time" name="jam_masuk" class="form-control" value="<?= $_POST['jam_masuk'] ?? '' ?>">
    </div>
    <div class="mb-3">
      <label>Jam Pulang</label>
      <input type="time" name="jam_pulang" class="form-control" value="<?= $_POST['jam_pulang'] ?? '' ?>">
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="lokasi_presensi.php" class="btn btn-secondary">Batal</a>
  </form>
</div>

<?php include('../layout/footer.php'); ?>

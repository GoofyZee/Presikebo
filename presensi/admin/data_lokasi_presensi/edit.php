<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../config.php');

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: lokasi_presensi.php");
    exit;
}

$error = '';
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lokasi   = trim($_POST['nama_lokasi']);
    $alamat_lokasi = trim($_POST['alamat_lokasi']);
    $tipe_lokasi   = trim($_POST['tipe_lokasi']);
    $latitude      = trim($_POST['latitude']);
    $longitude     = trim($_POST['longitude']); // perbaikan nama kolom
    $radius        = isset($_POST['radius']) && is_numeric($_POST['radius']) ? (int)$_POST['radius'] : null;
    $zona_waktu    = trim($_POST['zona_waktu']);
    $jam_masuk     = $_POST['jam_masuk'];
    $jam_pulang    = $_POST['jam_pulang'];

    // Validasi
    if ($nama_lokasi === '') {
        $error = "Nama Lokasi wajib diisi.";
    } elseif ($radius === null || $radius <= 0) {
        $error = "Radius harus angka lebih dari 0.";
    } elseif (!$jam_masuk || !$jam_pulang) {
        $error = "Jam masuk dan jam pulang wajib diisi.";
    } elseif (strtotime($jam_masuk) >= strtotime($jam_pulang)) {
        $error = "Jam pulang harus setelah jam masuk.";
    } else {
        $stmt = $connection->prepare("UPDATE lokasi_presensi SET nama_lokasi=?, alamat_lokasi=?, tipe_lokasi=?, latitude=?, longitude=?, radius=?, zona_waktu=?, jam_masuk=?, jam_pulang=? WHERE id=?");
        $stmt->bind_param("sssssisssi", $nama_lokasi, $alamat_lokasi, $tipe_lokasi, $latitude, $longitude, $radius, $zona_waktu, $jam_masuk, $jam_pulang, $id);
        $exec = $stmt->execute();
        $stmt->close();

        if ($exec) {
            header("Location: lokasi_presensi.php?status=success&action=edit");
            exit;
        } else {
            $error = "Gagal mengupdate data.";
        }
    }

    // Kalau error, isi ulang form dengan POST
    $data = $_POST;
} else {
    // Ambil data dari DB
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
}

include('../layout/header.php');
?>

<div class="container mt-4">
  <h3>Edit Lokasi Presensi</h3>

  <?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label>Nama Lokasi</label>
      <input type="text" name="nama_lokasi" class="form-control" required value="<?= htmlspecialchars($data['nama_lokasi'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label>Alamat Lokasi</label>
      <input type="text" name="alamat_lokasi" class="form-control" value="<?= htmlspecialchars($data['alamat_lokasi'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label>Tipe Lokasi</label>
      <input type="text" name="tipe_lokasi" class="form-control" value="<?= htmlspecialchars($data['tipe_lokasi'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label>Latitude</label>
      <input type="text" name="latitude" class="form-control" value="<?= htmlspecialchars($data['latitude'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label>Longitude</label>
      <input type="text" name="longitude" class="form-control" value="<?= htmlspecialchars($data['longitude'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label>Radius (meter)</label>
      <input type="number" name="radius" class="form-control" value="<?= htmlspecialchars($data['radius'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label>Zona Waktu</label>
      <input type="text" name="zona_waktu" maxlength="4" class="form-control" value="<?= htmlspecialchars($data['zona_waktu'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label>Jam Masuk</label>
      <input type="time" name="jam_masuk" class="form-control" value="<?= htmlspecialchars($data['jam_masuk'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label>Jam Pulang</label>
      <input type="time" name="jam_pulang" class="form-control" value="<?= htmlspecialchars($data['jam_pulang'] ?? '') ?>">
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="lokasi_presensi.php" class="btn btn-secondary">Batal</a>
  </form>
</div>

<?php include('../layout/footer.php'); ?>
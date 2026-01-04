<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../config.php');
include('../layout/header.php');

$pesan = '';
$pegawai = mysqli_query($connection, "SELECT id, nama FROM pegawai ORDER BY nama");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pegawai = $_POST['id_pegawai'];
    $tanggal = $_POST['tanggal'];
    $jam_masuk = $_POST['jam_masuk'];
    $jam_keluar = $_POST['jam_keluar'];

    $stmt = $connection->prepare("INSERT INTO presensi (id_pegawai, tanggal_masuk, jam_masuk, tanggal_keluar, jam_keluar, foto_masuk, foto_keluar)
                                  VALUES (?, ?, ?, ?, ?, '', '')");
    $stmt->bind_param("issss", $id_pegawai, $tanggal, $jam_masuk, $tanggal, $jam_keluar);

    if ($stmt->execute()) {
        $pesan = "<div class='alert alert-success'>Presensi manual berhasil ditambahkan.</div>";
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal menyimpan data.</div>";
    }
}
?>

<div class="container py-4">
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Input Presensi Manual</h5>
    </div>
    <div class="card-body">
      <?= $pesan ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Nama Pegawai</label>
          <select name="id_pegawai" class="form-select" required>
            <option value="">-- Pilih Pegawai --</option>
            <?php while ($p = mysqli_fetch_assoc($pegawai)): ?>
              <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Tanggal</label>
          <input type="date" name="tanggal" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Jam Masuk</label>
          <input type="time" name="jam_masuk" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Jam Keluar</label>
          <input type="time" name="jam_keluar" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Presensi</button>
      </form>
    </div>
  </div>
</div>

<?php include('../layout/footer.php'); ?>

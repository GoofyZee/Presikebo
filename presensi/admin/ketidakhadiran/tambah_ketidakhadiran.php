<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../config.php');
include('../layout/header.php');

$id_pegawai = $_SESSION['id'];
$pesan = '';

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keterangan = $_POST['keterangan'];
    $deskripsi = $_POST['deskripsi'];
    $tanggal = $_POST['tanggal'];
    $file_name = null;

    // Proses upload file jika ada
    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $file_name = uniqid('surat_', true) . '.' . $ext;
            $upload_path = "../../uploads/ketidakhadiran/" . $file_name;

            if (!move_uploaded_file($_FILES['file']['tmp_name'], $upload_path)) {
                $pesan = "<div class='alert alert-danger'>Gagal mengunggah file surat.</div>";
                $file_name = null;
            }
        } else {
            $pesan = "<div class='alert alert-warning'>Format file tidak didukung. Gunakan PDF/JPG/PNG.</div>";
        }
    }

    // Simpan data ke DB jika tidak ada pesan error
    if (!$pesan) {
        $stmt = $connection->prepare("INSERT INTO ketidakhadiran 
            (id_pegawai, keterangan, tanggal, deskripsi, file, status_pengajuan) 
            VALUES (?, ?, ?, ?, ?, 'Menunggu')");
        $stmt->bind_param("issss", $id_pegawai, $keterangan, $tanggal, $deskripsi, $file_name);

        if ($stmt->execute()) {
            $pesan = "<div class='alert alert-success'>Pengajuan berhasil dikirim.</div>";
        } else {
            $pesan = "<div class='alert alert-danger'>Gagal menyimpan data.</div>";
        }
    }
}
?>

<div class="container py-4">
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Form Pengajuan Ketidakhadiran</h5>
    </div>
    <div class="card-body">
      <?= $pesan ?>
      <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="keterangan" class="form-label">Keterangan</label>
          <select name="keterangan" id="keterangan" class="form-select" required>
            <option value="">-- Pilih --</option>
            <option value="Cuti">Cuti</option>
            <option value="Izin">Izin</option>
            <option value="Sakit">Sakit</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="tanggal" class="form-label">Tanggal</label>
          <input type="date" name="tanggal" id="tanggal" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="deskripsi" class="form-label">Deskripsi</label>
          <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
          <label for="file" class="form-label">Surat Keterangan (Opsional)</label>
          <input type="file" name="file" id="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        </div>
        <button type="submit" class="btn btn-primary">Ajukan</button>
        <a href="ketidakhadiran.php" class="btn btn-secondary">Kembali</a>
      </form>
    </div>
  </div>
</div>

<?php include('../layout/footer.php'); ?>

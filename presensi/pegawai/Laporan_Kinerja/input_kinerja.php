<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'pegawai') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../config.php');
include('../layout/header.php');

$id_pegawai = $_SESSION['id'];
$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'];
    $uraian = $_POST['uraian'];
    $output = $_POST['output'];
    $bukti_nama = null;

    // Upload bukti
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === 0) {
        $allowed_ext = ['jpg', 'jpeg', 'png'];
        $file_ext = strtolower(pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION));
        $max_size = 2 * 1024 * 1024; // 2MB
        $file_size = $_FILES['bukti']['size'];

        // Cek validasi file
        if (!in_array($file_ext, $allowed_ext)) {
            $pesan = "<div class='alert alert-warning'>Format file tidak didukung. Hanya JPG/PNG.</div>";
        } elseif ($file_size > $max_size) {
            $pesan = "<div class='alert alert-warning'>Ukuran file maksimal 2MB.</div>";
        } elseif (!getimagesize($_FILES['bukti']['tmp_name'])) {
            $pesan = "<div class='alert alert-warning'>File bukan gambar yang valid.</div>";
        } else {
            // Buat folder jika belum ada
            $folder = '../../uploads/kinerja/';
            if (!is_dir($folder)) {
                mkdir($folder, 0775, true);
            }

            $bukti_nama = uniqid('bukti_', true) . '.' . $file_ext;
            $upload_path = $folder . $bukti_nama;

            if (!move_uploaded_file($_FILES['bukti']['tmp_name'], $upload_path)) {
                $pesan = "<div class='alert alert-danger'>Gagal mengupload bukti gambar.</div>";
                $bukti_nama = null;
            }
        }
    }

    // Simpan ke database
    if (!$pesan) {
        $stmt = $connection->prepare("INSERT INTO kinerja (id_pegawai, tanggal, uraian, output, bukti) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $id_pegawai, $tanggal, $uraian, $output, $bukti_nama);

        if ($stmt->execute()) {
            $pesan = "<div class='alert alert-success'>Kinerja berhasil disimpan.</div>";
        } else {
            $pesan = "<div class='alert alert-danger'>Gagal menyimpan data ke database.</div>";
        }
    }
}
?>

<div class="container py-4">
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Input Kinerja Harian</h5>
    </div>
    <div class="card-body">
      <?= $pesan ?>
      <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="tanggal" class="form-label">Tanggal</label>
          <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="mb-3">
          <label for="uraian" class="form-label">Uraian Tugas</label>
          <textarea name="uraian" id="uraian" class="form-control" rows="3" required></textarea>
        </div>
        <div class="mb-3">
          <label for="output" class="form-label">Output (hasil pekerjaan)</label>
          <input type="text" name="output" id="output" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="bukti" class="form-label">Bukti Gambar (Opsional, JPG/PNG maks 2MB)</label>
          <input type="file" name="bukti" id="bukti" class="form-control" accept=".jpg,.jpeg,.png">
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </form>
    </div>
  </div>
</div>

<?php include('../layout/footer.php'); ?>

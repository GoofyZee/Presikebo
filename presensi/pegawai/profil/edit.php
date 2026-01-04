<?php
session_start();

// Cek login
if (!isset($_SESSION['login'], $_SESSION['user'], $_SESSION['role'])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
}

require_once('../../config.php');
$judul = "Edit Profil";

$user = $_SESSION['user'];
$id_user = $user['id'] ?? '';
$nama_lama = $user['nama'] ?? '';
$jk_lama = $user['jenis_kelamin'] ?? '';
$alamat_lama = $user['alamat'] ?? '';
$nohp_lama = $user['no_handphone'] ?? '';

$error = '';
$success = '';

// Tombol batal ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['batal'])) {
    header("Location: profil.php");
    exit;
}

// Tombol simpan ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $nama = htmlspecialchars(trim($_POST['nama']));
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $alamat = htmlspecialchars(trim($_POST['alamat']));
    $no_handphone = htmlspecialchars(trim($_POST['no_handphone']));

    if ($nama && $jenis_kelamin && $alamat && $no_handphone) {
        $query = "UPDATE pegawai SET nama = ?, jenis_kelamin = ?, alamat = ?, no_handphone = ? WHERE id = ?";
        $stmt = mysqli_prepare($connection, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssi", $nama, $jenis_kelamin, $alamat, $no_handphone, $id_user);
            $execute = mysqli_stmt_execute($stmt);

            if ($execute) {
                // Update session
                $_SESSION['user']['nama'] = $nama;
                $_SESSION['user']['jenis_kelamin'] = $jenis_kelamin;
                $_SESSION['user']['alamat'] = $alamat;
                $_SESSION['user']['no_handphone'] = $no_handphone;

                // Redirect ke profil dengan notifikasi
                header("Location: profil.php?pesan=update_berhasil");
                exit;
            } else {
                $error = "Gagal menyimpan perubahan. Silakan coba lagi.";
            }

            mysqli_stmt_close($stmt);
        } else {
            $error = "Gagal menyiapkan perintah database.";
        }
    } else {
        $error = "Semua field harus diisi!";
    }
}

include('../layout/header.php');
?>

<?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'update_berhasil'): ?>
<script>
  window.onload = function() {
    alert('Profil berhasil diperbarui!');
  }
</script>
<?php endif; ?>


<div class="main">
  <main class="content">
    <div class="container-fluid p-4">
      <h1 class="h3 mb-4">Edit Profil</h1>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
          <form method="POST" action="">
            <div class="mb-3">
              <label for="nama" class="form-label">Nama</label>
              <input type="text" class="form-control" id="nama" name="nama" required value="<?= htmlspecialchars($nama_lama) ?>">
            </div>
            <div class="mb-3">
              <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
              <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                <option value="">-- Pilih --</option>
                <option value="Laki-laki" <?= ($jk_lama === 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                <option value="Perempuan" <?= ($jk_lama === 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="alamat" class="form-label">Alamat</label>
              <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= htmlspecialchars($alamat_lama) ?></textarea>
            </div>
            <div class="mb-3">
              <label for="no_handphone" class="form-label">No. Handphone</label>
              <input type="text" class="form-control" id="no_handphone" name="no_handphone" required value="<?= htmlspecialchars($nohp_lama) ?>">
            </div>

            <div class="d-flex justify-content-between">
              <button type="submit" name="simpan" class="btn btn-success">Simpan Perubahan</button>
              <button type="submit" name="batal" class="btn btn-secondary">Batal</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>
</div>

<?php include('../layout/footer.php'); ?>

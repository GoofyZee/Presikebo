<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['login'], $_SESSION['user'], $_SESSION['role'])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
}

require_once('../../config.php');
$judul = "Edit Profil";

$user = $_SESSION['user'];
$id_user = $user['id'];
$error = '';

// Variabel lama
$nip_lama = $user['nip'] ?? '';
$nama_lama = $user['nama'] ?? '';
$jk_lama = $user['jenis_kelamin'] ?? '';
$alamat_lama = $user['alamat'] ?? '';
$nohp_lama = $user['no_handphone'] ?? '';
$jabatan_lama = $user['jabatan'] ?? '';
$lokasi_lama = $user['lokasi_presensi'] ?? '';
$foto_lama = $user['foto'] ?? 'avatar.jpg';

// Handle batal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['batal'])) {
    header("Location: index.php");
    exit;
}

// Handle simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $nip = trim($_POST['nip']);
    $nama = trim($_POST['nama']);
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $alamat = trim($_POST['alamat']);
    $no_handphone = trim($_POST['no_handphone']);
    $jabatan = trim($_POST['jabatan']);
    $lokasi = trim($_POST['lokasi_presensi']);

    $foto_baru = $foto_lama;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['foto']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {
            $foto_baru = uniqid('foto_', true) . '.' . $ext;
            move_uploaded_file($tmp, "../../assets/img/avatars/" . $foto_baru);
        } else {
            $error = "Format foto tidak didukung (JPG, PNG, WEBP).";
        }
    }

    if (!$error) {
        $stmt = mysqli_prepare($connection, "UPDATE pegawai SET nip = ?, nama = ?, jenis_kelamin = ?, alamat = ?, no_handphone = ?, jabatan = ?, lokasi_presensi = ?, foto = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssssssssi", $nip, $nama, $jenis_kelamin, $alamat, $no_handphone, $jabatan, $lokasi, $foto_baru, $id_user);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['user'] = array_merge($_SESSION['user'], [
                'nip' => $nip,
                'nama' => $nama,
                'jenis_kelamin' => $jenis_kelamin,
                'alamat' => $alamat,
                'no_handphone' => $no_handphone,
                'jabatan' => $jabatan,
                'lokasi_presensi' => $lokasi,
                'foto' => $foto_baru
            ]);

            header("Location: index.php?pesan=update_berhasil");
            exit;
        } else {
            $error = "Gagal menyimpan ke database.";
        }
    }
}

include('../layout/header.php');
?>

<div class="main">
  <main class="content">
    <div class="container-fluid p-4">
      <h1 class="h3 mb-4">Edit Profil Admin</h1>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
          <form method="POST" enctype="multipart/form-data">
            <div class="text-center mb-4">
              <img src="../../assets/img/avatars/<?= htmlspecialchars($foto_lama) ?>" class="rounded-circle" width="100" height="100" alt="Foto Profil">
              <input type="file" name="foto" class="form-control mt-2" accept="image/*">
            </div>

            <div class="mb-3">
              <label class="form-label">NIP</label>
              <input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($nip_lama) ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Nama</label>
              <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($nama_lama) ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Jenis Kelamin</label>
              <select name="jenis_kelamin" class="form-select" required>
                <option value="">-- Pilih --</option>
                <option value="Laki-laki" <?= $jk_lama === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="Perempuan" <?= $jk_lama === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Alamat</label>
              <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($alamat_lama) ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">No. Handphone</label>
              <input type="text" name="no_handphone" class="form-control" value="<?= htmlspecialchars($nohp_lama) ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Jabatan</label>
              <input type="text" name="jabatan" class="form-control" value="<?= htmlspecialchars($jabatan_lama) ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Lokasi Presensi</label>
              <input type="text" name="lokasi_presensi" class="form-control" value="<?= htmlspecialchars($lokasi_lama) ?>" required>
            </div>

            <div class="d-flex justify-content-between">
              <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
              <button type="submit" name="batal" class="btn btn-secondary">Batal</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>
</div>

<?php include('../layout/footer.php'); ?>

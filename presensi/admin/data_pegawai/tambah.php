<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}
require_once('../../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip             = trim($_POST['nip']);
    $nama            = trim($_POST['nama']);
    $jenis_kelamin   = trim($_POST['jenis_kelamin']);
    $alamat          = trim($_POST['alamat']);
    $no_handphone    = trim($_POST['no_handphone']);
    $jabatan         = trim($_POST['jabatan']);
    $lokasi_presensi = trim($_POST['lokasi_presensi']);
    $username        = trim($_POST['username']);
    $password        = trim($_POST['password']);
    $role            = trim($_POST['role']);
    $status          = 'aktif';

    if (
        empty($nip) || empty($nama) || empty($jenis_kelamin) || empty($alamat) || empty($no_handphone) ||
        empty($jabatan) || empty($lokasi_presensi) || empty($username) || empty($password) || empty($role)
    ) {
        $error = "Semua field wajib diisi.";
    } else {
        $foto_name = '';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto_name = 'pegawai_' . time() . '.' . $ext;
            $upload_path = '../../uploads/foto/' . $foto_name;
            move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path);
        }

        // Insert pegawai
        $stmt1 = $connection->prepare("INSERT INTO pegawai (nip, nama, jenis_kelamin, alamat, no_handphone, jabatan, lokasi_presensi, foto)
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt1->bind_param("ssssssss", $nip, $nama, $jenis_kelamin, $alamat, $no_handphone, $jabatan, $lokasi_presensi, $foto_name);
        $stmt1->execute();
        $id_pegawai = $stmt1->insert_id;
        $stmt1->close();

        // Insert users
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt2 = $connection->prepare("INSERT INTO users (id_pegawai, username, password, status, role)
                                       VALUES (?, ?, ?, ?, ?)");
        $stmt2->bind_param("issss", $id_pegawai, $username, $hashed_password, $status, $role);
        $stmt2->execute();
        $stmt2->close();

        header("Location: data_pegawai.php?status=success&action=add");
        exit;
    }
}
include('../layout/header.php');
?>

<div class="container mt-4">
  <h3>Tambah Data Pegawai</h3>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="" enctype="multipart/form-data">
    <div class="row">
      <div class="col-md-6">
        <div class="mb-3">
          <label for="nip">NIP</label>
          <input type="text" name="nip" id="nip" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="nama">Nama</label>
          <input type="text" name="nama" id="nama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="jenis_kelamin">Jenis Kelamin</label>
          <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" required>
            <option value="">-- Pilih --</option>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="alamat">Alamat</label>
          <textarea name="alamat" id="alamat" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
          <label for="no_handphone">No. HP</label>
          <input type="text" name="no_handphone" id="no_handphone" class="form-control" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="mb-3">
          <label for="jabatan">Jabatan</label>
          <input type="text" name="jabatan" id="jabatan" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="lokasi_presensi">Lokasi Presensi</label>
          <textarea name="lokasi_presensi" id="lokasi_presensi" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
          <label for="username">Username</label>
          <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="role">Role</label>
          <select name="role" id="role" class="form-control" required>
            <option value="admin">Admin</option>
            <option value="pegawai">Pegawai</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="foto">Foto</label>
          <input type="file" name="foto" id="foto" class="form-control">
        </div>
      </div>
    </div>
    <div class="d-flex justify-content-between mt-3">
      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="data_pegawai.php" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>

<?php include('../layout/footer.php'); ?>

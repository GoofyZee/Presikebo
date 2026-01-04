<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../config.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$id) {
    header("Location: data_pegawai.php");
    exit;
}

$query = "SELECT p.*, u.role FROM pegawai p
          JOIN users u ON p.id = u.id_pegawai WHERE p.id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: pegawai.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $password = trim($_POST['password']);
    $jabatan = trim($_POST['jabatan']);
    $role = trim($_POST['role']);
    $jenis_kelamin = trim($_POST['jenis_kelamin']);
    $lokasi_presensi = trim($_POST['lokasi_presensi']);

    if (empty($nama) || empty($jabatan) || empty($role) || empty($jenis_kelamin) || empty($lokasi_presensi)) {
        $error = "Semua field wajib diisi.";
    } else {
        // Upload foto baru (jika ada)
        $foto_name = $data['foto'];
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto_name = 'pegawai_' . time() . '.' . $ext;
            $upload_path = '../../uploads/foto/' . $foto_name;

            if (!empty($data['foto']) && file_exists('../../uploads/foto/' . $data['foto'])) {
                unlink('../../uploads/foto/' . $data['foto']);
            }

            move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path);
        }

        // Update data pegawai
        $stmt_pegawai = $connection->prepare("UPDATE pegawai SET nama = ?, jabatan = ?, jenis_kelamin = ?, lokasi_presensi = ?, foto = ? WHERE id = ?");
        $stmt_pegawai->bind_param("sssssi", $nama, $jabatan, $jenis_kelamin, $lokasi_presensi, $foto_name, $id);
        $stmt_pegawai->execute();
        $stmt_pegawai->close();

        // Update data user
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt_user = $connection->prepare("UPDATE users SET password = ?, role = ? WHERE id_pegawai = ?");
            $stmt_user->bind_param("ssi", $hashed_password, $role, $id);
        } else {
            $stmt_user = $connection->prepare("UPDATE users SET role = ? WHERE id_pegawai = ?");
            $stmt_user->bind_param("si", $role, $id);
        }
        $stmt_user->execute();
        $stmt_user->close();

        header("Location: pegawai.php?status=success&action=edit");
        exit;
    }
}

include('../layout/header.php');
?>

<div class="container mt-4">
  <h3>Edit Data Pegawai</h3>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="" enctype="multipart/form-data">
    <div class="row">
      <!-- Kolom 1 -->
      <div class="col-md-6">
        <div class="mb-3">
          <label for="nama">Nama</label>
          <input type="text" name="nama" id="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
        </div>
        <div class="mb-3">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" class="form-control">
          <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
        </div>
        <div class="mb-3">
          <label for="jabatan">Jabatan</label>
          <input type="text" name="jabatan" id="jabatan" class="form-control" value="<?= htmlspecialchars($data['jabatan']) ?>" required>
        </div>
        <div class="mb-3">
          <label for="role">Role</label>
          <select name="role" id="role" class="form-control" required>
            <option value="admin" <?= $data['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="pegawai" <?= $data['role'] === 'pegawai' ? 'selected' : '' ?>>Pegawai</option>
          </select>
        </div>
      </div>

      <!-- Kolom 2 -->
      <div class="col-md-6">
        <div class="mb-3">
          <label for="jenis_kelamin">Jenis Kelamin</label>
          <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" required>
            <option value="Laki-laki" <?= $data['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="Perempuan" <?= $data['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="lokasi_presensi">Lokasi Presensi</label>
          <textarea name="lokasi_presensi" id="lokasi_presensi" class="form-control" required><?= htmlspecialchars($data['lokasi_presensi']) ?></textarea>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-between mt-3">
      <button type="submit" class="btn btn-warning">Update</button>
      <a href="data_pegawai.php" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>

<?php include('../layout/footer.php'); ?>

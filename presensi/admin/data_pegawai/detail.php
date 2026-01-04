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

$query = "SELECT p.*, u.role
          FROM pegawai p
          JOIN users u ON p.id = u.id_pegawai
          WHERE p.id = ?";
$stmt = $connection->prepare($query);

if (!$stmt) {
    die("Query error: " . $connection->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: data_pegawai.php");
    exit;
}

include('../layout/header.php');
?>

<div class="container mt-4">
  <h3>Detail Data Pegawai</h3>

  <div class="card">
    <div class="card-body">
      <table class="table table-bordered">
        <tbody>
          <tr>
            <th>Nama</th>
            <td><?= htmlspecialchars($data['nama']) ?></td>
          </tr>
          <tr>
            <th>Jenis Kelamin</th>
            <td><?= htmlspecialchars($data['jenis_kelamin'] ?? 'Tidak Diketahui') ?></td>
          </tr>
          <tr>
            <th>Jabatan</th>
            <td><?= htmlspecialchars($data['jabatan']) ?></td>
          </tr>
          <tr>
            <th>Lokasi Presensi</th>
            <td><?= nl2br(htmlspecialchars($data['lokasi_presensi'] ?? 'Tidak Diketahui')) ?></td>
          </tr>
          <tr>
            <th>Role</th>
            <td><?= htmlspecialchars($data['role']) ?></td>
          </tr>
        </tbody>
      </table>

      <div class="d-flex justify-content-between mt-3">
        <a href="pegawai.php" class="btn btn-secondary">Kembali</a>
        <div>
          <a href="edit.php?id=<?= urlencode($id) ?>" class="btn btn-warning">Edit</a>
          <a href="hapus.php?id=<?= urlencode($id) ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('../layout/footer.php'); ?>
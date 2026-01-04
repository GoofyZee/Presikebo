<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../config.php');
include('../layout/header.php');

$id_pegawai = $_SESSION['id'];

// Ambil data ketidakhadiran dari DB
$query = "SELECT * FROM ketidakhadiran WHERE id_pegawai = ? ORDER BY tanggal DESC";
$stmt = $connection->prepare($query);
$stmt->bind_param('i', $id_pegawai);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container py-4">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Pengajuan Ketidakhadiran</h5>
      <a href="tambah_ketidakhadiran.php" class="btn btn-sm btn-success">+ Ajukan</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>Keterangan</th>
              <th>Tanggal</th>
              <th>Deskripsi</th>
              <th>Surat</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . htmlspecialchars($row['keterangan']) . "</td>";
                echo "<td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>";
                echo "<td>" . htmlspecialchars($row['deskripsi']) . "</td>";

                if (!empty($row['file']) && file_exists("../../uploads/ketidakhadiran/" . $row['file'])) {
                  $url = "../../uploads/ketidakhadiran/" . htmlspecialchars($row['file']);
                  echo "<td><a href='$url' target='_blank'>📎 Lihat</a></td>";
                } else {
                  echo "<td><span class='text-muted'>-</span></td>";
                }

                echo "<td>" . htmlspecialchars($row['status_pengajuan'] ?? 'Menunggu') . "</td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='6'><em>Belum ada pengajuan ketidakhadiran.</em></td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include('../layout/footer.php'); ?>

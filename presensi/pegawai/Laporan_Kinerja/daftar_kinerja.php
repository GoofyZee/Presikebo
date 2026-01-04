<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'pegawai') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../config.php');
include('../layout/header.php');

$id_pegawai = $_SESSION['id'];

// Ambil data kinerja pegawai
$query = "SELECT * FROM kinerja WHERE id_pegawai = ? ORDER BY tanggal DESC";
$stmt = $connection->prepare($query);
$stmt->bind_param('i', $id_pegawai);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container py-4">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Daftar Kinerja</h5>
      <a href="input_kinerja.php" class="btn btn-sm btn-success">+ Tambah Kinerja</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>Tanggal</th>
              <th>Uraian</th>
              <th>Output</th>
              <th>Bukti</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>";
                echo "<td>" . htmlspecialchars($row['uraian']) . "</td>";
                echo "<td>" . htmlspecialchars($row['output']) . "</td>";

                if (!empty($row['bukti']) && file_exists("../../uploads/kinerja/" . $row['bukti'])) {
                  $bukti_url = "../../uploads/kinerja/" . htmlspecialchars($row['bukti']);
                  echo "<td><a href='$bukti_url' target='_blank'><img src='$bukti_url' alt='Bukti' width='100'></a></td>";
                } else {
                  echo "<td><span class='text-muted'>Tidak ada</span></td>";
                }

                echo "<td>
                        <a href='edit_kinerja.php?id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                        <a href='hapus_kinerja.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin ingin menghapus data ini?\")'>Hapus</a>
                      </td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='6'>Belum ada data kinerja.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include('../layout/footer.php'); ?>

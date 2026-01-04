<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once('../../config.php');

// Filter input
$bulan = $_GET['bulan'] ?? date('Y-m');
$id_pegawai = $_GET['pegawai'] ?? '';

// Query
$where = "DATE_FORMAT(tanggal_masuk, '%Y-%m') = ?";
$params = [$bulan];
$types = 's';

if ($id_pegawai) {
    $where .= " AND id_pegawai = ?";
    $params[] = $id_pegawai;
    $types .= 'i';
}

$sql = "SELECT p.*, u.nama, u.nip FROM presensi p
        JOIN pegawai u ON p.id_pegawai = u.id
        WHERE $where ORDER BY tanggal_masuk ASC";
$stmt = $connection->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Output Excel headers
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rekap_presensi_" . $bulan . ".xls");

echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>Nama</th>
        <th>NIP</th>
        <th>Tanggal</th>
        <th>Jam Masuk</th>
        <th>Jam Keluar</th>
        <th>Terlambat</th>
        <th>Total Jam</th>
      </tr>";

$no = 1;
while ($row = $result->fetch_assoc()) {
    $jam_masuk = $row['jam_masuk'];
    $jam_keluar = $row['jam_keluar'];
    $telat = strtotime($jam_masuk) - strtotime('07:30:00');
    $telat_str = $telat > 0 ? floor($telat / 3600) . ' jam ' . floor(($telat % 3600) / 60) . ' mnt' : '-';

    $total_str = '-';
    if ($jam_masuk && $jam_keluar && $jam_keluar !== '00:00:00') {
        $selisih = strtotime($jam_keluar) - strtotime($jam_masuk);
        $total_str = floor($selisih / 3600) . ' jam ' . floor(($selisih % 3600) / 60) . ' mnt';
    }

    echo "<tr>
            <td>{$no}</td>
            <td>" . htmlspecialchars($row['nama']) . "</td>
            <td>" . htmlspecialchars($row['nip']) . "</td>
            <td>" . date('d-m-Y', strtotime($row['tanggal_masuk'])) . "</td>
            <td>{$jam_masuk}</td>
            <td>{$jam_keluar}</td>
            <td>{$telat_str}</td>
            <td>{$total_str}</td>
          </tr>";
    $no++;
}
echo "</table>";
?>

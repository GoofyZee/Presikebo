<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../config.php');
include('../layout/header.php');

$tanggal_hari_ini = date('Y-m-d');

// Ambil data total pegawai
$q_total_pegawai = mysqli_query($connection, "SELECT COUNT(*) as total FROM pegawai");
$total_pegawai = mysqli_fetch_assoc($q_total_pegawai)['total'] ?? 0;

// Total hadir hari ini
$q_total_hadir = mysqli_query($connection, "SELECT COUNT(*) as total FROM presensi WHERE tanggal_masuk = '$tanggal_hari_ini'");
$total_hadir = mysqli_fetch_assoc($q_total_hadir)['total'] ?? 0;

// Total ketidakhadiran hari ini
$q_total_izin = mysqli_query($connection, "
    SELECT COUNT(*) as total FROM ketidakhadiran 
    WHERE tanggal = '$tanggal_hari_ini' AND status_pengajuan = 'disetujui'
");
$total_izin = mysqli_fetch_assoc($q_total_izin)['total'] ?? 0;

// Hitung total alfa = total pegawai - hadir - izin/sakit/cuti
$total_alfa = max(0, $total_pegawai - $total_hadir - $total_izin);
?>

<div class="main">
  <main class="content">
    <div class="container-fluid p-4">
      <h1 class="h3 mb-4"><strong>Home</strong></h1>
      <div class="container py-4">
        <h2 class="mb-4">Selamat Datang, <?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></h2>

        <div class="row g-3 mb-4">
          <!-- Total Pegawai -->
          <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
              <div class="card-body d-flex align-items-center">
                <i data-feather="users" class="me-3 text-primary" style="font-size: 24px;"></i>
                <div>
                  <h6 class="mb-0">Total Pegawai</h6>
                  <small><?= $total_pegawai ?> Pegawai Terdaftar</small>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Hadir -->
          <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
              <div class="card-body d-flex align-items-center">
                <i data-feather="check-circle" class="me-3 text-success" style="font-size: 24px;"></i>
                <div>
                  <h6 class="mb-0">Hadir Hari Ini</h6>
                  <small><?= $total_hadir ?> Pegawai</small>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Alfa -->
          <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
              <div class="card-body d-flex align-items-center">
                <i data-feather="x-circle" class="me-3 text-danger" style="font-size: 24px;"></i>
                <div>
                  <h6 class="mb-0">Alfa Hari Ini</h6>
                  <small><?= $total_alfa ?> Pegawai</small>
                </div>
              </div>
            </div>
          </div>

          <!-- Izin / Cuti / Sakit -->
          <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
              <div class="card-body d-flex align-items-center">
                <i data-feather="file-text" class="me-3 text-warning" style="font-size: 24px;"></i>
                <div>
                  <h6 class="mb-0">Cuti/Izin/Sakit</h6>
                  <small><?= $total_izin ?> Pegawai</small>
                </div>
              </div>
            </div>
          </div>
        </div> <!-- end row -->
      </div>
    </div>
  </main>
</div>

<?php include('../layout/footer.php'); ?>

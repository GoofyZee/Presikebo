<?php
session_start();

// Cek autentikasi dan otorisasi
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'pegawai') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../config.php');
include('../layout/header.php');

// Ambil ID Pegawai dari data user
$id_pegawai = $_SESSION['user']['id_pegawai'] ?? null;
$tanggal_hari_ini = date('Y-m-d');
$presensi = null;

if ($id_pegawai) {
    $query = "SELECT * FROM presensi WHERE id_pegawai = '$id_pegawai' AND tanggal_masuk = '$tanggal_hari_ini'";
    $result = mysqli_query($connection, $query);
    $presensi = mysqli_fetch_assoc($result);
}
?>

<!-- Custom Responsive Styling -->
<style>
  @media (max-width: 576px) {
    #real-time-clock {
      font-size: 1.75rem !important;
    }
    .card-title {
      font-size: 1rem;
    }
    .btn {
      font-size: 0.9rem;
      padding: 0.5rem 1rem;
    }
    .display-4 {
      font-size: 2rem !important;
    }
  }
</style>

<div class="main">
  <main class="content">
    <div class="container-fluid p-4">
      <h1 class="h3 mb-4"><strong>Dashboard Presensi</strong></h1>

      <div class="container py-4">
        <h2 class="mb-4">Selamat Datang, 
          <span class="text-primary fw-bold"><?= htmlspecialchars($_SESSION['nama'] ?? 'Pegawai'); ?></span>
        </h2>

        <div class="row g-3 mb-4">
          <!-- Presensi Masuk -->
          <div class="col-md-6 col-12">
            <div class="card shadow-sm border-0 rounded-3 h-100">
              <div class="card-body text-center">
                <h5 class="card-title">Presensi Masuk</h5>
                <?php if (!empty($presensi['jam_masuk'])): ?>
                  <div class="text-success mt-3">
                    <i class="fas fa-check-circle fa-2x"></i>
                    <p class="fw-bold mt-2">Anda telah presensi masuk</p>
                    <small class="text-muted"><?= htmlspecialchars($presensi['jam_masuk']); ?></small>
                  </div>
                <?php else: ?>
                  <div class="text-danger mt-3">
                    <i class="fas fa-times-circle fa-2x"></i>
                    <p class="fw-bold mt-2">Belum melakukan presensi masuk</p>
                  </div>
                  <form action="../presensi/presensi_masuk.php" method="GET">
                    <button type="submit" class="btn btn-primary mt-3 w-100 rounded-pill fw-bold">
                      <i class="fas fa-sign-in-alt me-2"></i>Presensi Masuk
                    </button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Presensi Keluar -->
          <div class="col-md-6 col-12">
            <div class="card shadow-sm border-0 rounded-3 h-100">
              <div class="card-body text-center">
                <h5 class="card-title">Presensi Keluar</h5>
                <?php if (!empty($presensi['jam_keluar'])): ?>
                  <div class="text-success mt-3">
                    <i class="fas fa-check-circle fa-2x"></i>
                    <p class="fw-bold mt-2">Anda telah presensi keluar</p>
                    <small class="text-muted"><?= htmlspecialchars($presensi['jam_keluar']); ?></small>
                  </div>
                <?php elseif (!empty($presensi['jam_masuk'])): ?>
                  <div class="text-danger mt-3">
                    <i class="fas fa-times-circle fa-2x"></i>
                    <p class="fw-bold mt-2">Belum melakukan presensi keluar</p>
                  </div>
                  <form action="../presensi/presensi_keluar.php" method="GET">
                    <button type="submit" class="btn btn-danger mt-3 w-100 rounded-pill fw-bold">
                      <i class="fas fa-sign-out-alt me-2"></i>Presensi Keluar
                    </button>
                  </form>
                <?php else: ?>
                  <div class="text-muted mt-3">
                    <i class="fas fa-info-circle fa-2x"></i>
                    <p class="fw-bold mt-2">Presensi masuk terlebih dahulu</p>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Jam & Tanggal Sekarang -->
        <div class="card shadow-sm border-0 rounded-3">
          <div class="card-body text-center">
            <h5 class="fw-bold mb-3">Waktu Sekarang</h5>
            <h2 id="real-time-clock" class="fw-bold text-primary display-6 mb-2" style="word-break: break-word;"></h2>
            <small id="real-time-date" class="text-muted fs-6"></small>
          </div>
        </div>

        <!-- Lokasi Pengguna -->
        <div class="card shadow-sm border-0 rounded-3 mt-3">
          <div class="card-body text-center">
            <h5 class="fw-bold mb-2">Lokasi Anda</h5>
            <div id="user-location" class="text-muted fs-6 text-break">Mengambil lokasi...</div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Scripts -->
<script>
function updateWaktu() {
  const now = new Date();
  const jam = String(now.getHours()).padStart(2, '0');
  const menit = String(now.getMinutes()).padStart(2, '0');
  const detik = String(now.getSeconds()).padStart(2, '0');
  const tanggal = now.toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
  document.getElementById('real-time-clock').textContent = `${jam}:${menit}:${detik}`;
  document.getElementById('real-time-date').textContent = tanggal;
}
setInterval(updateWaktu, 1000);
updateWaktu();

// Lokasi
function getLocation() {
  const locationEl = document.getElementById('user-location');

  if (!navigator.geolocation) {
    locationEl.textContent = 'Geolocation tidak didukung oleh browser ini.';
    return;
  }

  navigator.geolocation.getCurrentPosition(
    (position) => {
      const latitude = position.coords.latitude;
      const longitude = position.coords.longitude;

      locationEl.innerHTML = `
        <i class="fas fa-map-marker-alt"></i> 
        Lat: ${latitude.toFixed(5)} | Lng: ${longitude.toFixed(5)}
        <br />
        <a href="https://www.google.com/maps?q=${latitude},${longitude}" target="_blank">Lihat di Google Maps</a>
      `;
    },
    (error) => {
      switch(error.code) {
        case error.PERMISSION_DENIED:
          locationEl.textContent = 'Izin lokasi ditolak.';
          break;
        case error.POSITION_UNAVAILABLE:
          locationEl.textContent = 'Informasi lokasi tidak tersedia.';
          break;
        case error.TIMEOUT:
          locationEl.textContent = 'Permintaan lokasi habis waktu.';
          break;
        case error.UNKNOWN_ERROR:
        default:
          locationEl.textContent = 'Terjadi kesalahan saat mendapatkan lokasi.';
      }
    }
  );
}
getLocation();
</script>

<?php include('../layout/footer.php'); ?>

<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'pegawai') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}
require_once('../../config.php');
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Presensi Keluar</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Leaflet CSS & JS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <style>
    #map {
      width: 100%;
      height: 300px;
      border-radius: 0.5rem;
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen p-6">

  <div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow-md">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">Presensi Keluar</h2>
      <a href="../../auth/logout.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Keluar</a>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
      <!-- Map -->
      <div class="lg:w-1/2">
        <div id="map" class="mb-3"></div>
        <div id="datetime" class="text-sm text-gray-600"></div>
      </div>

      <!-- Kamera -->
      <div class="lg:w-1/2">
        <video id="camera" autoplay playsinline class="rounded-lg mb-2 w-full"></video>
        <canvas id="snapshot" class="hidden"></canvas>

        <div class="flex gap-3">
          <button id="btnCapture" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Ambil Foto</button>
          <button id="btnSubmit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Keluar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Hidden Form -->
  <form id="formPresensiKeluar" style="display:none;">
    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">
    <input type="hidden" name="foto_base64" id="foto_base64">
  </form>

  <script>
    // Tanggal dan Waktu
    function updateDateTime() {
      const now = new Date();
      const tanggal = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
      const waktu = now.toLocaleTimeString('id-ID');
      document.getElementById('datetime').textContent = `📅 ${tanggal} 🕒 ${waktu}`;
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    // Lokasi + Map
    let map, marker;

    navigator.geolocation.getCurrentPosition((pos) => {
      const lat = pos.coords.latitude;
      const lon = pos.coords.longitude;

      document.getElementById('latitude').value = lat;
      document.getElementById('longitude').value = lon;

      map = L.map('map').setView([lat, lon], 17);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map);

      marker = L.marker([lat, lon]).addTo(map)
        .bindPopup("Lokasi Anda")
        .openPopup();
    }, (err) => {
      console.error("Geolocation error:", err);
      alert("Gagal mendapatkan lokasi (" + err.code + "): " + err.message);
    });

    // Kamera
    const video = document.getElementById('camera');
    const canvas = document.getElementById('snapshot');
    const context = canvas.getContext('2d');

    navigator.mediaDevices.getUserMedia({ video: true })
      .then(stream => video.srcObject = stream)
      .catch(err => alert("Kamera tidak tersedia: " + err.message));

    // Ambil Foto
    document.getElementById('btnCapture').addEventListener('click', function () {
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      context.drawImage(video, 0, 0, canvas.width, canvas.height);
      document.getElementById('foto_base64').value = canvas.toDataURL('image/jpeg');
      alert('Foto berhasil diambil!');
    });

    // Submit Presensi
    document.getElementById('btnSubmit').addEventListener('click', function () {
      const form = document.getElementById('formPresensiKeluar');
      const data = new FormData(form);

      fetch('proses_presensi_keluar.php', {
        method: 'POST',
        body: data
      })
        .then(res => res.json())
        .then(data => {
          alert(data.message);
          if (data.success) window.location.href = '../home/index.php';
        })
        .catch(err => {
          alert('Gagal mengirim presensi');
          console.error(err);
        });
    });
  </script>

</body>
</html>

<?php include('../layout/footer.php'); ?>
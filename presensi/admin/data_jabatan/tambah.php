<?php
session_start();

// Cek login & admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jabatan = trim($_POST['jabatan']);

    if ($jabatan === '') {
        header("Location: tambah.php?status=fail");
        exit;
    }

    $stmt = $connection->prepare("INSERT INTO jabatan (jabatan) VALUES (?)");
    $stmt->bind_param("s", $jabatan);
    $exec = $stmt->execute();
    $stmt->close();

    if ($exec) {
        header("Location: jabatan.php?status=success&action=add");
    } else {
        header("Location: tambah.php?status=fail");
    }
    exit;
}

// Header minimal supaya bersih tapi tetap ada tag html dasar & bootstrap (sesuaikan path css)
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tambah Jabatan - Aplikasi Presensi</title>
  <link href="../../assets/css/app.css" rel="stylesheet" />
</head>
<body>
  <div class="container p-4">
    <h3>Tambah Jabatan</h3>
    <?php if (isset($_GET['status']) && $_GET['status'] === 'fail'): ?>
      <div class="alert alert-danger">Input jabatan tidak boleh kosong atau gagal menyimpan data.</div>
    <?php endif; ?>
    <form action="tambah.php" method="POST">
      <div class="mb-3">
        <label for="jabatan" class="form-label">Nama Jabatan</label>
        <input type="text" id="jabatan" name="jabatan" class="form-control" required autofocus>
      </div>
      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="jabatan.php" class="btn btn-secondary">Batal</a>
    </form>
  </div>

  <script src="../../assets/js/app.js"></script>
</body>
</html>

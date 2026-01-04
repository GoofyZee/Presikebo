<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

require_once('../../config.php');

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: jabatan.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jabatan = trim($_POST['jabatan']);
    if ($jabatan === '') {
        header("Location: edit.php?id=$id&status=fail");
        exit;
    }
    $stmt = $connection->prepare("UPDATE jabatan SET jabatan = ? WHERE id = ?");
    $stmt->bind_param("si", $jabatan, $id);
    $exec = $stmt->execute();
    $stmt->close();

    if ($exec) {
        header("Location: jabatan.php?status=success&action=edit");
    } else {
        header("Location: edit.php?id=$id&status=fail");
    }
    exit;
}

// Ambil data jabatan lama
$stmt = $connection->prepare("SELECT jabatan FROM jabatan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($jabatan_lama);
$stmt->fetch();
$stmt->close();

// Header sederhana & minimal
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Jabatan - Aplikasi Presensi</title>
  <link href="../../assets/css/app.css" rel="stylesheet" />
</head>
<body>
  <div class="container p-4">
    <h3>Edit Jabatan</h3>
    <?php if (isset($_GET['status']) && $_GET['status'] === 'fail'): ?>
      <div class="alert alert-danger">Input jabatan tidak boleh kosong atau gagal menyimpan data.</div>
    <?php endif; ?>
    <form action="edit.php?id=<?= htmlspecialchars($id) ?>" method="POST">
      <div class="mb-3">
        <label for="jabatan" class="form-label">Nama Jabatan</label>
        <input type="text" id="jabatan" name="jabatan" class="form-control" value="<?= htmlspecialchars($jabatan_lama) ?>" required autofocus>
      </div>
      <button type="submit" class="btn btn-warning">Update</button>
      <a href="jabatan.php" class="btn btn-secondary">Batal</a>
    </form>
  </div>

  <script src="../../assets/js/app.js"></script>
</body>
</html>

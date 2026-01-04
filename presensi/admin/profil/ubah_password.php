<?php
session_start();
require_once('../../config.php');

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
}

$judul = "Ubah Password";
$errors = [];
$success = "";

// Handle tombol Batal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['batal'])) {
    header("Location: ../dashboard.php");
    exit;
}

// Handle perubahan password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $password_lama = $_POST['password_lama'] ?? '';
    $password_baru = $_POST['password_baru'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';
    $username = $_SESSION['username'];

    // Ambil password lama dari database
    $stmt = mysqli_prepare($connection, "SELECT password FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$data) {
        $errors[] = "User tidak ditemukan.";
    } elseif (!password_verify($password_lama, $data['password'])) {
        $errors[] = "Password lama salah.";
    } elseif (strlen($password_baru) < 6) {
        $errors[] = "Password baru minimal 6 karakter.";
    } elseif ($password_baru !== $konfirmasi) {
        $errors[] = "Konfirmasi password tidak cocok.";
    } else {
        // Update password
        $hash = password_hash($password_baru, PASSWORD_DEFAULT);
        $stmt_update = mysqli_prepare($connection, "UPDATE users SET password = ? WHERE username = ?");
        mysqli_stmt_bind_param($stmt_update, "ss", $hash, $username);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);

        $success = "Password berhasil diubah.";
    }
}

include('../layout/header.php');
?>

<div class="main">
  <main class="content">
    <div class="container p-4" style="max-width: 500px;">
      <h4 class="mb-4">Ubah Password</h4>

      <?php if ($errors): ?>
        <div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
      <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="mb-3">
          <label>Password Lama</label>
          <input type="password" name="password_lama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Password Baru</label>
          <input type="password" name="password_baru" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Konfirmasi Password Baru</label>
          <input type="password" name="konfirmasi" class="form-control" required>
        </div>
        <div class="d-flex justify-content-between">
          <button class="btn btn-primary" type="submit" name="simpan">Simpan</button>
          <button class="btn btn-secondary" type="submit" name="batal">Batal</button>
        </div>
      </form>
    </div>
  </main>
</div>

<?php include('../layout/footer.php'); ?>

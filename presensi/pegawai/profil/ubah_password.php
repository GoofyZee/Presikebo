<?php
session_start();
require_once('../../config.php');
if (!isset($_SESSION['login'])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
}
$judul = "Ubah Password";
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi = $_POST['konfirmasi'];

    $username = $_SESSION['username'];
    $query = mysqli_query($connection, "SELECT password FROM users WHERE username='$username'");
    $data = mysqli_fetch_assoc($query);

    if (!password_verify($password_lama, $data['password'])) {
        $errors[] = "Password lama salah.";
    } elseif ($password_baru !== $konfirmasi) {
        $errors[] = "Konfirmasi password tidak cocok.";
    } else {
        $hash = password_hash($password_baru, PASSWORD_DEFAULT);
        mysqli_query($connection, "UPDATE users SET password='$hash' WHERE username='$username'");
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
        <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
      <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
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
        <button class="btn btn-primary" type="submit">Simpan</button>
      </form>
    </div>
  </main>
</div>

<?php include('../layout/footer.php'); ?>

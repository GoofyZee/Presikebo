<?php
session_start();
require_once('../config.php');

$alert = '';

// Notifikasi dari URL
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] === 'belum_login') {
        $alert = "Swal.fire('Akses Ditolak', 'Silakan login terlebih dahulu!', 'warning');";
    } elseif ($_GET['pesan'] === 'tolak_akses') {
        $alert = "Swal.fire('Akses Ditolak', 'Anda tidak punya akses ke halaman tersebut!', 'error');";
    }
}

// Proses Login
if (isset($_POST['login'])) {
    $username = trim($_POST['Username']);
    $password = trim($_POST['Password']);

    if ($username === '' || $password === '') {
        $alert = "Swal.fire('Gagal', 'Username dan password wajib diisi!', 'warning');";
    } else {
        $stmt = $connection->prepare("
            SELECT users.*, pegawai.*
            FROM users
            JOIN pegawai ON CAST(users.id_pegawai AS CHAR) = pegawai.id
            WHERE users.username = ?
        ");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Set semua data ke dalam session
                $_SESSION['login']      = true;
                $_SESSION['user_id']    = $user['id']; // ID dari tabel users
                $_SESSION['id']         = $user['id_pegawai']; // ID dari pegawai
                $_SESSION['username']   = $user['username'];
                $_SESSION['role']       = strtolower(trim($user['role']));
                $_SESSION['nama']       = $user['nama'];
                $_SESSION['nip']        = $user['nip'];

                // Simpan seluruh data pegawai ke $_SESSION['user']
                $_SESSION['user'] = [
                    'id'            => $user['id'],
                    'id_pegawai'    => $user['id_pegawai'],
                    'nama'          => $user['nama'],
                    'nip'           => $user['nip'],
                    'jenis_kelamin' => $user['jenis_kelamin'],
                    'alamat'        => $user['alamat'],
                    'no_handphone'  => $user['no_handphone'],
                    'foto'          => $user['foto'],
                    'role'          => strtolower(trim($user['role']))
                ];

                // Jika centang "Remember Me"
                if (isset($_POST['remember-me'])) {
                    setcookie("username", $username, time() + (86400 * 30), "/");
                }

                // Redirect ke dashboard sesuai role
                if ($_SESSION['role'] === 'admin') {
                    header("Location: ../admin/home/index.php");
                    exit;
                } elseif ($_SESSION['role'] === 'pegawai') {
                    header("Location: ../pegawai/home/index.php");
                    exit;
                } else {
                    $alert = "Swal.fire('Gagal', 'Role tidak dikenali!', 'error');";
                }
            } else {
                $alert = "Swal.fire('Gagal', 'Password salah!', 'error');";
            }
        } else {
            $alert = "Swal.fire('Gagal', 'Username tidak ditemukan!', 'error');";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Masuk | Aplikasi Absensi Kecamatan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../assets/css/app.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background: url('../assets/img/bg1.jpg') center / cover no-repeat fixed; font-family: 'Inter', sans-serif;">
    <main class="login-container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card shadow-lg p-4" style="border-radius: 12px;">
            <div class="text-center mb-4">
                <img src="../assets/img/logo.png" alt="Logo" style="max-height: 80px;">
                <h3>Selamat Datang 👋</h3>
                <p class="text-muted">Masuk untuk melanjutkan ke sistem presensi</p>
            </div>

            <form method="post" autocomplete="off">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input class="form-control" type="text" id="username" name="Username"
                           value="<?= isset($_COOKIE['username']) ? htmlspecialchars($_COOKIE['username']) : '' ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input class="form-control" type="password" id="password" name="Password" required>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember-me"
                           <?= isset($_COOKIE['username']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="remember">Ingat Saya</label>
                </div>

                <div class="d-grid">
                    <button type="submit" name="login" class="btn btn-primary">Masuk</button>
                </div>
            </form>

            <footer class="text-center mt-4" style="font-size: 13px;">&copy; 2025 Kecamatan Bojongsari — Versi 1.0</footer>
        </div>
    </main>

    <?php if (!empty($alert)) : ?>
        <script><?= $alert ?></script>
    <?php endif; ?>

    <script src="../assets/js/app.js"></script>
</body>
</html>
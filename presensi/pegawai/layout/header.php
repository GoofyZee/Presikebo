<?php
require_once('../../config.php');
if (session_status() === PHP_SESSION_NONE) session_start();

// Autentikasi pengguna
if (!isset($_SESSION['login'])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
}

// Data Profil
$nama = htmlspecialchars($_SESSION['nama'] ?? 'Pengguna');
$role = htmlspecialchars($_SESSION['role'] ?? 'Pegawai');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?=isset($judul) ? htmlspecialchars($judul) . ' - Aplikasi Presensi' : 'Aplikasi Presensi'?></title>
    <link rel="shortcut icon" href="<?= base_url('assets/img/icons/kebo.png') ?>">
    <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <style>
        .navbar-brand img {
            background-color: transparent;
            object-fit: contain;
            padding: 2px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease;
        }
        .navbar-brand img:hover { transform: scale(1.05); }
        .dropdown-menu { z-index: 1050; }
        .nav-item .nav-link { transition: all 0.3s ease; }
        .nav-item .nav-link:hover { transform: translateY(-2px); opacity: 0.9; }
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-profile-img {
            width: 40px; height: 40px;
            border-radius: 50%; border: 2px solid rgba(255, 255, 255, 0.3);
            object-fit: cover; transition: all 0.3s ease;
        }
        .user-profile:hover .user-profile-img {
            border-color: rgba(255, 255, 255, 0.7);
            transform: scale(1.05);
        }
        .user-profile-info { display: flex; flex-direction: column; line-height: 1.2; }
        .user-profile-name { font-weight: 600; font-size: 0.95rem; }
        .user-profile-role { font-size: 0.75rem; opacity: 0.8; }
        .nav-link.active::after {
            content: ''; position: absolute;
            bottom: -5px; left: 0; right: 0;
            height: 2px; background-color: #fff; border-radius: 2px;
        }
    </style>

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="<?= base_url('pegawai/home/index.php') ?>">
            <img src="<?= base_url('assets/img/logo.png?v=2') ?>"
                alt="Logo Kecamatan Bojongsari" width="50" height="50">
        </a>

        <!-- Toggle Button -->
        <button class="navbar-toggler" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#topNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

       <!-- Menu Navigasi -->
<div class="collapse navbar-collapse" id="topNavbar">
    <ul class="navbar-nav me-auto">
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('pegawai/home/index.php') ?>">
                <i data-feather="home"></i> Home
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" 
               href="#" 
               role="button" 
               data-bs-toggle="dropdown">
                <i data-feather="calendar"></i> Laporan Presensi
            </a>
           <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="<?= base_url('pegawai/laporan_presensi/hari_ini.php') ?>">
                <i data-feather="clock" class="me-1"></i> Presensi Hari Ini
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="<?= base_url('pegawai/laporan_presensi/rekap_presensi.php') ?>">
                <i data-feather="file-text" class="me-1"></i> Rekap Presensi
                </a>
            </li>
            </ul>

        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" 
               href="#" 
               role="button" 
               data-bs-toggle="dropdown">
                <i data-feather="bar-chart-2"></i> Laporan Kinerja
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="<?= base_url('pegawai/laporan_kinerja/input_kinerja.php') ?>">
                    <i data-feather="edit-2" class="me-1"></i> Input
                </a></li>
                <li><a class="dropdown-item" href="<?= base_url('pegawai/laporan_kinerja/daftar_kinerja.php') ?>">
                    <i data-feather="list" class="me-1"></i> Daftar
                </a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('pegawai/ketidakhadiran/ketidakhadiran.php') ?>">
                <i data-feather="user-x"></i> Ketidakhadiran
            </a>
        </li>
    </ul>
</div>



            <!-- Profil Pengguna -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center py-1" 
                       href="#" 
                       role="button" 
                       data-bs-toggle="dropdown" 
                       aria-expanded="false">
                        <div class="user-profile">
                            <img src="<?= base_url('assets/img/avatars/avatar.jpg') ?>"
                                 class="user-profile-img"
                                 alt="<?= $nama ?>"
                                 onerror="this.src='<?= base_url('assets/img/avatars/default.jpg') ?>'" />
                            <div class="user-profile-info d-none d-lg-block">
                                <span class="user-profile-name"><?= $nama ?></span>
                                <span class="user-profile-role"><?= $role ?></span>
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <h6 class="dropdown-header text-center">
                                <div class="d-flex justify-content-center mb-2">
                                    <img src="<?= base_url('assets/img/avatars/avatar.jpg') ?>"
                                         class="rounded-circle"
                                         width="60"
                                         height="60"
                                         alt="<?= $nama ?>"
                                         onerror="this.src='<?= base_url('assets/img/avatars/default.jpg') ?>'" />
                                </div>
                                <div class="fw-bold"><?= $nama ?></div>
                                <small><?= $role ?></small>
                            </h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/profil/index.php') ?>">
                            <i data-feather="user" class="me-2"></i> Profil Saya</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/profil/ubah_password.php') ?>">
                            <i data-feather="lock" class="me-2"></i> Ubah Password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('auth/logout.php') ?>">
                            <i data-feather="log-out" class="me-2"></i> Keluar</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Aktifkan Feather Icons & Highlight Menu Aktif -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        const currentPage = location.pathname.split("/").pop();
        document.querySelectorAll(".nav-link").forEach(link => {
            if (link.getAttribute("href") && link.getAttribute("href").includes(currentPage)) {
                link.classList.add("active");
            }
        });
    });
</script>
</body>
</html>

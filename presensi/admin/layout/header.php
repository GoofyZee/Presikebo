<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once('../../config.php');

// Check if user is not logged in
if (!isset($_SESSION['login'])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
}

// Get user data from session
$nama = htmlspecialchars($_SESSION['nama'] ?? 'User');
$role = htmlspecialchars($_SESSION['role'] ?? 'Role');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= isset($judul) ? htmlspecialchars($judul) . ' - Aplikasi Presensi' : 'Aplikasi Presensi' ?></title>
    <link rel="shortcut icon" href="<?= base_url('assets/img/icons/kebo.png') ?>" />
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
        .navbar-brand img:hover {
            transform: scale(1.05);
        }
        .dropdown-menu {
            z-index: 1050;
        }
        .dropdown-item.active {
            background-color: #343a40;
            color: #fff;
        }
        .nav-item a.nav-link {
            transition: all 0.3s ease;
        }
        .nav-item a.nav-link:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.3);
            object-fit: cover;
            transition: all 0.3s ease;
        }
        .user-profile:hover .user-profile-img {
            border-color: rgba(255,255,255,0.7);
            transform: scale(1.05);
        }
        .user-profile-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }
        .user-profile-name {
            font-weight: 600;
            font-size: 0.95rem;
        }
        .user-profile-role {
            font-size: 0.75rem;
            opacity: 0.8;
        }
        .nav-link.active {
            position: relative;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= base_url('admin/home/index.php') ?>">
            <img src="<?= base_url('assets/img/logo.png?v=2') ?>" alt="Logo Kecamatan Bojongsari" width="50" height="50">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="topNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('admin/home/index.php') ?>">
                        <i data-feather="home"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('admin/data_pegawai/pegawai.php') ?>">
                        <i data-feather="users"></i> Pegawai
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i data-feather="database"></i> Master Data
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= base_url('admin/data_jabatan/jabatan.php') ?>"><i data-feather="briefcase"></i> Jabatan</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/data_lokasi_presensi/lokasi_presensi.php') ?>"><i data-feather="map-pin"></i> Lokasi Presensi</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i data-feather="calendar"></i> Rekap Presensi
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= base_url('admin/rekap_presensi/rekap_harian.php') ?>"><i data-feather="calendar"></i> Rekap Harian</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/rekap_presensi/rekap_bulanan.php') ?>"><i data-feather="calendar"></i> Rekap Bulanan</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/rekap_presensi/manual_presensi.php') ?>"><i data-feather="edit-3"></i> Input Manual</a></li>
                        
                    </ul>
                </li>
                            <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('admin/ketidakhadiran/ketidakhadiran.php') ?>">
                        <i data-feather="user-x"></i> Ketidakhadiran
                    </a>
        </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center py-1" href="#" data-bs-toggle="dropdown">
                        <div class="user-profile">
                            <img src="<?= base_url('assets/img/avatars/avatar.jpg') ?>" class="user-profile-img" alt="<?= $nama ?>" onerror="this.src='<?= base_url('assets/img/avatars/default.jpg') ?>'">
                            <div class="user-profile-info d-none d-lg-block">
                                <span class="user-profile-name"><?= $nama ?></span>
                                <span class="user-profile-role"><?= $role ?></span>
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="text-center p-2">
                            <img src="<?= base_url('assets/img/avatars/avatar.jpg') ?>" class="rounded-circle" width="60" height="60" alt="<?= $nama ?>" onerror="this.src='<?= base_url('assets/img/avatars/default.jpg') ?>'">
                            <div class="fw-bold mt-2"><?= $nama ?></div>
                            <small><?= $role ?></small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/profil/index.php') ?>"><i data-feather="user"></i> Profil Saya</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/profil/ubah_password.php') ?>"><i data-feather="lock"></i> Ubah Password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('auth/logout.php') ?>"><i data-feather="log-out"></i> Keluar</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        const currentPath = location.pathname.split('/').pop();
        const links = document.querySelectorAll('.nav-link, .dropdown-item');
        links.forEach(link => {
            const href = link.getAttribute('href');
            if (!href) return;
            const hrefPath = href.split('/').pop();
            if (hrefPath === currentPath) {
                link.classList.add('active');
                const parentDropdown = link.closest('.dropdown-menu');
                if (parentDropdown) {
                    const dropdownToggle = parentDropdown.previousElementSibling;
                    if (dropdownToggle) dropdownToggle.classList.add('active');
                }
            }
        });
    });
</script>
</body>
</html>

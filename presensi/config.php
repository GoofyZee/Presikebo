<?php

$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; // Kosongkan jika tidak pakai password
$db_name = 'presensi'; // Sesuaikan dengan nama database kamu

$connection = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if (!$connection) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

function base_url($url = null)
{
    $base_url = 'http://localhost/presensi'; // Perbaikan: http:// bukan http:http//

    if ($url != null) {
        return $base_url . '/' . ltrim($url, '/');
    } else {
        return $base_url;
    }
}
?>

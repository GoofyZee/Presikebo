<?php
header("Content-Type: application/json");

if ($_POST['username'] === 'admin' && $_POST['password'] === 'admin') {
    echo json_encode([
        "success" => true,
        "role" => "admin"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Login gagal"
    ]);
}

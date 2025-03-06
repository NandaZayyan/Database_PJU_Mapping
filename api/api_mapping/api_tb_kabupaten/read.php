<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$koneksi = new mysqli('localhost', 'root', '', 'mapping_daerah_db');

if ($koneksi->connect_error) {
    die(json_encode(["status" => "error", "message" => "Gagal terhubung ke database: " . $koneksi->connect_error]));
}

$query = $koneksi->query("SELECT * FROM tb_kabupaten");

if (!$query) {
    die(json_encode(["status" => "error", "message" => "Gagal mengambil data: " . $koneksi->error]));
}

$data = $query->fetch_all(MYSQLI_ASSOC);

$koneksi->close();

echo json_encode($data);
?>

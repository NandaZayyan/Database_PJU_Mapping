<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Koneksi ke database
$koneksi = new mysqli('localhost', 'root', '', 'mapping_daerah_db');

// Periksa koneksi
if ($koneksi->connect_error) {
    die(json_encode(["status" => "error", "message" => "Gagal terhubung ke database: " . $koneksi->connect_error]));
}

// Cek apakah `id_kabupaten` diberikan dalam permintaan GET
$id_kabupaten = isset($_GET['id_kabupaten']) ? intval($_GET['id_kabupaten']) : 0;

// Query untuk mengambil data kecamatan berdasarkan `id_kabupaten`
if ($id_kabupaten > 0) {
    $query = $koneksi->prepare("SELECT * FROM tb_kecamatan WHERE id_kabupaten = ?");
    $query->bind_param("i", $id_kabupaten);
} else {
    $query = $koneksi->prepare("SELECT * FROM tb_kecamatan");
}

$query->execute();
$result = $query->get_result();

// Ambil data dan ubah menjadi array asosiatif
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Tutup koneksi database
$query->close();
$koneksi->close();

// Kirim data dalam format JSON
echo json_encode($data);
?>

<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Koneksi ke database
$koneksi = new mysqli('localhost', 'root', '', 'mapping_daerah_db');

// Periksa koneksi database
if ($koneksi->connect_error) {
    die(json_encode(["status" => "error", "message" => "Gagal terhubung ke database: " . $koneksi->connect_error]));
}

// Ambil parameter id_kecamatan jika ada
$id_kecamatan = isset($_GET['id_kecamatan']) ? intval($_GET['id_kecamatan']) : 0;
if ($id_kecamatan < 0) {
    die(json_encode(["status" => "error", "message" => "ID kecamatan tidak valid"]));
}

// Query untuk mengambil data dengan join ke `tb_kecamatan`
if ($id_kecamatan > 0) {
    $query = $koneksi->prepare("
        SELECT m.*, k.nama_kecamatan 
        FROM tb_mapping m
        JOIN tb_kecamatan k ON m.id_kecamatan = k.id_kecamatan
        WHERE m.id_kecamatan = ?
    ");
    $query->bind_param("i", $id_kecamatan);
} else {
    // Jika id_kecamatan tidak diberikan, kembalikan semua data
    $query = $koneksi->prepare("
        SELECT m.*, k.nama_kecamatan 
        FROM tb_mapping m
        JOIN tb_kecamatan k ON m.id_kecamatan = k.id_kecamatan
    ");
}

// Eksekusi query
if (!$query->execute()) {
    die(json_encode(["status" => "error", "message" => "Gagal menjalankan query: " . $query->error]));
}

$result = $query->get_result();

// Konversi hasil query ke array asosiatif
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Tutup koneksi database
$query->close();
$koneksi->close();

// Kirim data dalam format JSON
if (empty($data)) {
    $response = [
        "status" => "success",
        "message" => "Tidak ada data yang ditemukan",
        "data" => []
    ];
} else {
    $response = [
        "status" => "success",
        "data" => $data
    ];
}

echo json_encode($response);
?>
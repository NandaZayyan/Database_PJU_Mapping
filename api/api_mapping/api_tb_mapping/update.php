<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Koneksi ke database
$koneksi = new mysqli('localhost', 'root', '', 'mapping_daerah_db');

// Periksa koneksi database
if ($koneksi->connect_error) {
    die(json_encode(["status" => "error", "message" => "Gagal terhubung ke database: " . $koneksi->connect_error]));
}

// Ambil data dari body request
$data = json_decode(file_get_contents("php://input"), true);

// Validasi data yang diterima
$required_fields = ['ID_data']; // ID_data diperlukan untuk mengidentifikasi data yang akan diupdate
foreach ($required_fields as $field) {
    if (!isset($data[$field])) {
        die(json_encode(["status" => "error", "message" => "Field $field diperlukan"]));
    }
}

// Escape data untuk mencegah SQL injection
$ID_data = intval($data['ID_data']);
$lampu = isset($data['lampu']) ? $koneksi->real_escape_string($data['lampu']) : null;
$jenis_lampu = isset($data['jenis_lampu']) ? $koneksi->real_escape_string($data['jenis_lampu']) : null;
$jumlah_watt = isset($data['jumlah_watt']) ? intval($data['jumlah_watt']) : null;
$KWH_Meter = isset($data['KWH_Meter']) ? $koneksi->real_escape_string($data['KWH_Meter']) : null;
$No_KWH_Meter = isset($data['No_KWH_Meter']) ? $koneksi->real_escape_string($data['No_KWH_Meter']) : null;
$MCB = isset($data['MCB']) ? $koneksi->real_escape_string($data['MCB']) : null;
$arus_MCB = isset($data['arus_MCB']) ? intval($data['arus_MCB']) : null;
$kategori_PJU = isset($data['kategori_PJU']) ? $koneksi->real_escape_string($data['kategori_PJU']) : null;
$pengguna_lain = isset($data['pengguna_lain']) ? $koneksi->real_escape_string($data['pengguna_lain']) : null;
$jenis_penggunaan = isset($data['jenis_penggunaan']) ? $koneksi->real_escape_string($data['jenis_penggunaan']) : null;
$tanggal_survey = isset($data['tanggal_survey']) ? $koneksi->real_escape_string($data['tanggal_survey']) : null;

// Query untuk memperbarui data
$query = $koneksi->prepare("
    UPDATE tb_mapping
    SET
        lampu = COALESCE(?, lampu),
        jenis_lampu = COALESCE(?, jenis_lampu),
        jumlah_watt = COALESCE(?, jumlah_watt),
        KWH_Meter = COALESCE(?, KWH_Meter),
        No_KWH_Meter = COALESCE(?, No_KWH_Meter),
        MCB = COALESCE(?, MCB),
        arus_MCB = COALESCE(?, arus_MCB),
        kategori_PJU = COALESCE(?, kategori_PJU),
        pengguna_lain = COALESCE(?, pengguna_lain),
        jenis_penggunaan = COALESCE(?, jenis_penggunaan),
        tanggal_survey = COALESCE(?, tanggal_survey)
    WHERE ID_data = ?
");
$query->bind_param(
    "ssisssiisssi",
    $lampu, $jenis_lampu, $jumlah_watt, $KWH_Meter, $No_KWH_Meter,
    $MCB, $arus_MCB, $kategori_PJU, $pengguna_lain,
    $jenis_penggunaan, $tanggal_survey, $ID_data
);

// Eksekusi query
if (!$query->execute()) {
    die(json_encode(["status" => "error", "message" => "Gagal memperbarui data: " . $query->error]));
}

// Jika berhasil, kirim respons sukses
$response = [
    "status" => "success",
    "message" => "Data berhasil diperbarui",
    "data" => $data
];

echo json_encode($response);

// Tutup koneksi database
$query->close();
$koneksi->close();
?>
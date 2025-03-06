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
$required_fields = [
    'id_kecamatan', 'lampu', 'KWH_Meter', 'MCB', 'kategori_PJU',
    'pengguna_lain', 'tanggal_survey'
];

foreach ($required_fields as $field) {
    if (!isset($data[$field])) {
        die(json_encode(["status" => "error", "message" => "Field $field diperlukan"]));
    }
}

// Escape data untuk mencegah SQL injection
$id_kecamatan = intval($data['id_kecamatan']);
$lampu = $koneksi->real_escape_string($data['lampu']);
$jenis_lampu = isset($data['jenis_lampu']) ? $koneksi->real_escape_string($data['jenis_lampu']) : null;
$jumlah_watt = isset($data['jumlah_watt']) ? intval($data['jumlah_watt']) : null;
$KWH_Meter = $koneksi->real_escape_string($data['KWH_Meter']);
$No_KWH_Meter = isset($data['No_KWH_Meter']) ? $koneksi->real_escape_string($data['No_KWH_Meter']) : null;
$MCB = $koneksi->real_escape_string($data['MCB']);
$arus_MCB = isset($data['arus_MCB']) ? intval($data['arus_MCB']) : null;
$kategori_PJU = $koneksi->real_escape_string($data['kategori_PJU']);
$pengguna_lain = $koneksi->real_escape_string($data['pengguna_lain']);
$jenis_penggunaan = isset($data['jenis_penggunaan']) ? $koneksi->real_escape_string($data['jenis_penggunaan']) : null;
$tanggal_survey = $koneksi->real_escape_string($data['tanggal_survey']);

// Query untuk menambahkan data baru
$query = $koneksi->prepare("
    INSERT INTO tb_mapping (
        ID_kecamatan, lampu, jenis_lampu, jumlah_watt, KWH_Meter, No_KWH_Meter,
        MCB, arus_MCB, kategori_PJU, pengguna_lain,
        jenis_penggunaan, tanggal_survey
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$query->bind_param(
    "ississsiisss",
    $id_kecamatan, $lampu, $jenis_lampu, $jumlah_watt, $KWH_Meter, $No_KWH_Meter,
    $MCB, $arus_MCB, $kategori_PJU, $pengguna_lain,
    $jenis_penggunaan, $tanggal_survey
);

// Eksekusi query
if (!$query->execute()) {
    die(json_encode(["status" => "error", "message" => "Gagal menambahkan data: " . $query->error]));
}

// Jika berhasil, kirim respons sukses
$response = [
    "status" => "success",
    "message" => "Data berhasil ditambahkan",
    "data" => $data
];

echo json_encode($response);

// Tutup koneksi database
$query->close();
$koneksi->close();
?>
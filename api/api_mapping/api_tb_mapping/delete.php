<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// CORS Handling untuk preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Koneksi ke database
$servername = "localhost";
$username = "root";  // Sesuaikan dengan username MySQL
$password = "";       // Sesuaikan dengan password MySQL
$dbname = "mapping_daerah_db"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi database
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["status" => "error", "message" => "Gagal terhubung ke database: " . $conn->connect_error]));
}

// Baca JSON input
$json_input = file_get_contents("php://input");
$data = json_decode($json_input, true);

// Log Debugging untuk melihat request masuk
error_log("🔹 JSON Input: " . $json_input);
error_log("🔹 Decoded Data: " . print_r($data, true));

// Periksa apakah JSON valid
if ($data === null) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Format JSON tidak valid"]);
    exit;
}

// Pastikan ID_data ada dan valid
if (!isset($data['ID_data']) || empty($data['ID_data']) || !is_numeric($data['ID_data'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "ID_data tidak valid atau tidak ditemukan"]);
    exit;
}

// Escape untuk SQL injection
$ID_data = $conn->real_escape_string($data['ID_data']);

// Cek apakah data ada sebelum dihapus
$checkQuery = "SELECT * FROM tb_mapping WHERE ID_data = '$ID_data'";
$checkResult = $conn->query($checkQuery);

if ($checkResult->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Data tidak ditemukan"]);
    exit;
}

// Hapus data dari database
$sql = "DELETE FROM tb_mapping WHERE ID_data = '$ID_data'";

if ($conn->query($sql)) {
    http_response_code(200);
    echo json_encode(["status" => "success", "message" => "Data berhasil dihapus"]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Gagal menghapus data: " . $conn->error]);
}

// Tutup koneksi database
$conn->close();
?>

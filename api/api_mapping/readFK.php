<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

try {
    // Koneksi ke database
    $koneksi = new mysqli('localhost', 'root', '', 'mapping_daerah_db');

    // Periksa koneksi
    if ($koneksi->connect_error) {
        throw new Exception("Gagal terhubung ke database: " . $koneksi->connect_error);
    }

    // Ambil parameter id_kabupaten jika ada
    $id_kabupaten = isset($_GET['id_kabupaten']) ? intval($_GET['id_kabupaten']) : 0;

    // Persiapan query dengan ORDER BY
    if ($id_kabupaten > 0) {
        $stmt = $koneksi->prepare("SELECT * FROM tb_kecamatan WHERE id_kabupaten = ? ORDER BY id_kabupaten ASC");
        $stmt->bind_param("i", $id_kabupaten);
    } else {
        $stmt = $koneksi->prepare("SELECT * FROM tb_kecamatan ORDER BY id_kabupaten ASC");
    }

    // Eksekusi query
    $stmt->execute();
    $result = $stmt->get_result();

    // Ambil data sebagai array asosiatif
    $data = $result->fetch_all(MYSQLI_ASSOC);

    // Tutup koneksi database
    $stmt->close();
    $koneksi->close();

    // Tampilkan hasil dalam format JSON
    if (!empty($data)) {
        echo json_encode(["status" => "success", "data" => $data]);
    } else {
        echo json_encode(["status" => "error", "message" => "Data tidak ditemukan"]);
    }

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>

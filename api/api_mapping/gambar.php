<?php
// upload_image.php

// Supaya output API berformat JSON
header('Content-Type: application/json');

// Sertakan file koneksi
include 'read.php';

// Periksa apakah method request adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data JSON dari body request
    $json = file_get_contents('php://input');
    $jsonData = json_decode($json, true);

    // Validasi apakah JSON ter-parse
    if ($jsonData === null) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid JSON format"
        ]);
        exit;
    }

    // Ambil nilai dari JSON
    $IDdata    = isset($jsonData['ID_data']) ? $jsonData['ID_data'] : null;
    $imageData = isset($jsonData['file'])    ? $jsonData['file']    : null;

    // Validasi apakah ID_data dan file base64 ada
    if (empty($IDdata) || empty($imageData)) {
        echo json_encode([
            "status" => "error",
            "message" => "ID_data or file (base64) not provided"
        ]);
        exit;
    }

    // Decode base64 ke binary
    $imageDataDecoded = base64_decode($imageData);

    // Pastikan hasil decode tidak false (jika base64 salah)
    if ($imageDataDecoded === false) {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to decode base64"
        ]);
        exit;
    }

    // Direktori penyimpanan gambar
    $targetDir = __DIR__ . '../images/'; 
    // __DIR__ agar path selalu mengarah ke folder tempat file ini berada

    // Cek apakah folder images/ sudah ada, jika belum buat
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Menentukan tipe MIME file
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->buffer($imageDataDecoded);

    // Menentukan ekstensi file berdasarkan MIME type
    $extensions = [
        'image/jpeg' => '.jpg',
        'image/png'  => '.png',
        'image/gif'  => '.gif',
        'image/webp' => '.webp',
        'image/bmp'  => '.bmp'
    ];

    // Gunakan ekstensi yang sesuai, default ke .jpg jika tidak ditemukan
    $extension = isset($extensions[$mime]) ? $extensions[$mime] : '.jpg';

    // Membuat nama file berdasarkan ID_data (misalnya ID_data_123.jpg)
    $filename   = $IDdata . $extension;
    $targetFile = $targetDir . $filename;

    // Simpan gambar ke direktori
    if (file_put_contents($targetFile, $imageDataDecoded)) {
        try {
            // Update database dengan nama file baru
            $sql = "UPDATE tb_mapping 
                    SET mapping_image = :mappingImage 
                    WHERE ID_data = :IDdata";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":mappingImage", $filename);
            $stmt->bindParam(":IDdata", $IDdata, PDO::PARAM_INT);
            $stmt->execute();

            // Jika row yang ter-update lebih dari 0
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    "status"  => "success",
                    "message" => "Image uploaded successfully.",
                    "data"    => [
                        "ID_data"       => $IDdata,
                        "mapping_image" => $filename
                    ]
                ]);
            } else {
                // Mungkin ID_data tidak ditemukan di tabel
                echo json_encode([
                    "status"  => "error",
                    "message" => "No rows updated. Possibly invalid ID_data."
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                "status"  => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "Failed to upload image."
        ]);
    }
} else {
    // Jika bukan POST request
    echo json_encode([
        "status"  => "error",
        "message" => "Only POST method is allowed."
    ]);
}

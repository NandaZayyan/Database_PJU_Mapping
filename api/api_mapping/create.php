<?php

$koneksi = new mysqli('localhost', 'root','','mapping_daerah_db');
$nama_kabupaten = $_POST['nama_kabupaten'];
$data = mysqli_query($koneksi,"insert into tb_kabupaten set nama_kabupaten='$nama_kabupaten'");
if ($data) {
    echo json_encode([
        'pesan' => 'Sukses'
    ]); 
} else {
    echo json_encode([
        'pesan'=> 'Gagal'
        ]);
}
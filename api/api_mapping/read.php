<?php
$koneksi = new mysqli('localhost', 'root','','mapping_daerah_db');
$query = mysqli_query($koneksi,"select * from tb_kabupaten");
$data = mysqli_fetch_all( $query, MYSQLI_ASSOC);
echo json_encode($data);
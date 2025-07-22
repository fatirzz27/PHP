<?php

session_start();

include('koneksi.php');

$nama_barang     = $_POST['nama_barang'];
$kode_barang     = $_POST['kode_barang'];
$stock_barang    = $_POST['stock_barang'];
$lokasi_barang   = $_POST['lokasi_barang'];
$keadaan_barang  = $_POST['keadaan_barang'];

$allowed_keadaan = ['baik', 'jelek', 'hilang'];
if (!in_array($keadaan_barang, $allowed_keadaan)) {
    header("Location: input-barang.php?error=keadaan");
    exit;
}

$query  = "INSERT INTO tbl_barang (nama_barang, kode_barang, stock_barang, lokasi_barang, keadaan_barang) VALUES ('$nama_barang', '$kode_barang', '$stock_barang', '$lokasi_barang', '$keadaan_barang')";
$result = mysqli_query($connection, $query);

if($result) {
    header("Location: input-barang.php?success=1");
    exit;
} else {
    header("Location: input-barang.php?error=db");
    exit;
}

?>
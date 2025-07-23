<?php
include 'koneksi.php';
$id = $_POST['id_barang'];
$nama = $_POST['nama_barang'];
$kode = $_POST['kode_barang'];
$stok = $_POST['stock_barang'];
$lokasi = $_POST['lokasi_barang'];
$keadaan = $_POST['keadaan_barang'];

$query = mysqli_query($connection, "UPDATE tbl_barang SET 
    nama_barang='$nama', kode_barang='$kode', stock_barang='$stok', 
    lokasi_barang='$lokasi', keadaan_barang='$keadaan' WHERE id='$id'");

echo $query ? "success" : "error";

<?php
session_start();
include('koneksi.php');

// Cek login
if (!isset($_SESSION['id_user'])) {
    echo "error: unauthorized";
    exit;
}

// Ambil data POST
$nama_barang     = mysqli_real_escape_string($connection, $_POST['nama_barang']);
$kode_barang     = mysqli_real_escape_string($connection, $_POST['kode_barang']);
$stock_barang    = (int) $_POST['stock_barang'];
$lokasi_barang   = mysqli_real_escape_string($connection, $_POST['lokasi_barang']);
$keadaan_barang  = $_POST['keadaan_barang'];
$gambar_barang   = isset($_POST['gambar_barang_hidden']) ? $_POST['gambar_barang_hidden'] : null;

// Validasi keadaan
$allowed_keadaan = ['baik', 'jelek', 'hilang'];
if (!in_array($keadaan_barang, $allowed_keadaan)) {
    echo "error: keadaan tidak valid";
    exit;
}

// Validasi gambar
if (!$gambar_barang) {
    echo "error: gambar tidak boleh kosong";
    exit;
}

// Simpan ke database dengan kolom `gambar_barang`
$query = "INSERT INTO tbl_barang 
    (nama_barang, kode_barang, gambar_barang, stock_barang, lokasi_barang, keadaan_barang) 
    VALUES 
    ('$nama_barang', '$kode_barang','$gambar_barang', '$stock_barang', '$lokasi_barang', '$keadaan_barang' )";

$result = mysqli_query($connection, $query);

if ($result) {
    echo "success";
} else {
    echo "error: gagal menyimpan data";
}
?>

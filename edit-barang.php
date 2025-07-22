<?php

include 'koneksi.php';

$id = $_GET['id'];

if (!$id) {
    header("Location: input-barang.php?error=invalid_id");
    exit;
}
$result = mysqli_query($connection, "SELECT * FROM tbl_barang WHERE id = '$id'");
$data = mysqli_fetch_assoc($result);


if (!$data) {
    header("Location: input-barang.php?error=data_not_found");
    exit;
}

if (isset($_POST['update'])) {
    $nama_barang = $_POST['nama_barang'];
    $kode_barang = $_POST['kode_barang'];
    $stock_barang = $_POST['stock_barang'];
    $lokasi_barang = $_POST['lokasi_barang'];
    $keadaan_barang = $_POST['keadaan_barang'];

    $allowed_keadaan = ['baik', 'jelek', 'hilang'];
    if (!in_array($keadaan_barang, $allowed_keadaan)) {
        header("Location: edit-barang.php?id=$id&error=keadaan");
        exit;
    }

    $query = "UPDATE tbl_barang SET nama_barang='$nama_barang', kode_barang='$kode_barang', stock_barang='$stock_barang', lokasi_barang='$lokasi_barang', keadaan_barang='$keadaan_barang' WHERE id='$id'";

    $update = mysqli_query($connection, $query);
    if ($update) {
        header("Location: input-barang.php");
        exit;
    } else {
        echo "Gagal update data!";
    }
} else {
    // echo "Semua field harus diisi!";
}
?>






<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

    <title>Login Akun</title>
</head>

<body>

    <div class="container" style="margin-top: 50px">
        <div class="row">
            <div class="col-md-5 offset-md-3">
                <div class="card">
                    <div class="card-body">
                        <label>Edit data</label>
                        <hr>

                        <form method="POST" action="">
                            <div class="form-group">
                                <label>nama barang</label>
                                <input type="text" class="form-control" name="nama_barang" value="<?= $data['nama_barang']; ?>" placeholder="Masukkan nama_barang">
                            </div>

                            <div class="form-group">
                                <label>kode barang</label>
                                <input type="text" class="form-control" name="kode_barang" value="<?= $data['kode_barang']; ?>" placeholder="Masukkan kode_barang">
                            </div>

                            <div class="form-group">
                                <label>stock barang</label>
                                <input type="number" class="form-control" name="stock_barang" value="<?= $data['stock_barang']; ?>" placeholder="Masukkan stock_barang">
                            </div>

                            <div class="form-group">
                                <label>lokasi barang</label>
                                <input type="text" class="form-control" name="lokasi_barang" value="<?= $data['lokasi_barang']; ?>" placeholder="Masukkan lokasi_barang">
                            </div>

                            <div class="form-group">
                                <label>keadaan barang</label>
                                <select class="form-control" name="keadaan_barang">
                                    <option value="baik" <?= $data['keadaan_barang'] == 'baik' ? 'selected' : ''; ?>>Baik</option>
                                    <option value="jelek" <?= $data['keadaan_barang'] == 'jelek' ? 'selected' : ''; ?>>Jelek</option>
                                    <option value="hilang" <?= $data['keadaan_barang'] == 'hilang' ? 'selected' : ''; ?>>Hilang</option>
                                </select>
                            </div>

                            <button type="submit" name="update" class="btn btn-success btn-block">Submit</button>
                            <a href="input-barang.php" class="btn btn-danger btn-block">Batal</a>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
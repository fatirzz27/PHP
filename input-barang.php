<?php

session_start();

if (!$_SESSION['id_user']) {
    header("location: login.php");
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <title>Dashboard</title>
</head>

<body>
    <div class="container" style="margin-top: 50px">
        <div class="row">

            <div class="col-md-3">
                <ul class="list-group">
                    <li class="list-group-item active">MAIN MENU</li>
                    <a href="dashboard.php" class="list-group-item" style="color: #212529;">Dashboard</a>
                    <a href="input-barang.php" class="list-group-item" style="color: #212529;">Inventory</a>
                    <a href="logout.php" class="list-group-item" style="color: #212529;">Logout</a>
                </ul>
            </div>

            <div class="col-md-9">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Tambah Barang</h5>
                        <form id="formBarang" method="POST">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="nama_barang">Nama Barang</label>
                                    <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="kode_barang">Kode Barang</label>
                                    <input type="text" class="form-control" id="kode_barang" name="kode_barang" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="stock_barang">Stok Barang</label>
                                    <input type="number" class="form-control" id="stock_barang" name="stock_barang" required min="0">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="lokasi_barang">Lokasi Barang</label>
                                    <input type="text" class="form-control" id="lokasi_barang" name="lokasi_barang" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="keadaan_barang">Keadaan Barang</label>
                                    <select class="form-control" id="keadaan_barang" name="keadaan_barang" required>
                                        <option value="">Pilih Keadaan</option>
                                        <option value="baik">Baik</option>
                                        <option value="jelek">Jelek</option>
                                        <option value="hilang">Hilang</option>
                                    </select>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="fa fa-plus"></i> Tambah Barang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <label class="font-weight-bold">List Barang</label>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="tabelBarang">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Kode Barang</th>
                                        <th>Stok Barang</th>
                                        <th>Lokasi Barang</th>
                                        <th>Keadaan Barang</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="dataBarang">
                                    <?php
                                    include 'koneksi.php';
                                    $no = 1;
                                    $query = mysqli_query($connection, "SELECT * FROM tbl_barang");
                                    while ($row = mysqli_fetch_array($query)) {
                                        echo "<tr>
                                                    <td>" . $no++ . "</td>
                                                    <td>" . htmlspecialchars($row['nama_barang']) . "</td>
                                                    <td>" . htmlspecialchars($row['kode_barang']) . "</td>
                                                    <td>" . htmlspecialchars($row['stock_barang']) . "</td>
                                                    <td>" . htmlspecialchars($row['lokasi_barang']) . "</td>
                                                    <td>" . htmlspecialchars($row['keadaan_barang']) . "</td>
                                                    <td>
                                                        <div class='btn-group' role='group'>
                                                            <a href='edit-barang.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm mr-1'>
                                                            <i class='fa fa-edit'></i> Edit
                                                            </a>
                                                            <a href='delete-barang.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus data ini?')\">
                                                            <i class='fa fa-trash'></i> Delete
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>";
                                            }
                                        ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>


    <script>
        $(document).ready(function() {
            // AJAX untuk tambah barang tanpa reload
            $('#formBarang').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "cek-data.php",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.includes("success")) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Barang berhasil ditambahkan.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $('#formBarang')[0].reset();
                            reloadTableBarang();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Barang gagal ditambahkan.'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error!',
                            text: 'Terjadi kesalahan server.'
                        });
                    }
                });
            });

            // Fungsi reload tabel barang tanpa file baru
            function reloadTableBarang() {
                $.ajax({
                    url: window.location.href,
                    type: "GET",
                    dataType: "html",
                    success: function(data) {
                        // Ambil isi tbody dari hasil response
                        var newTbody = $(data).find("#dataBarang").html();
                        $("#dataBarang").html(newTbody);
                    }
                });
            }
        });
    </script>
</body>

</html>
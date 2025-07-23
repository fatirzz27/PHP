<?php
session_start();
if (!$_SESSION['id_user']) {
    header("location: login.php");
    exit;
}
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Vintage</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome & Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&family=EB+Garamond&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #fef8e7;
            font-family: 'EB Garamond', serif;
            color: #4b3e2b;
        }

        .vintage-card {
            background-color: #fffaf3;
            border: 1px solid #ccbfa3;
            box-shadow: 2px 4px 12px rgba(0, 0, 0, 0.1);
        }

        .vintage-header {
            font-family: 'Cinzel', serif;
            font-size: 1.4rem;
            color: #3f2f1b;
            border-bottom: 2px solid #b89b7d;
            margin-bottom: 1rem;
        }

        .list-group-item.active {
            background-color: #5b4636;
            border-color: #5b4636;
        }

        .list-group-item {
            background-color: #e8dbc4;
            border-color: #c6b49c;
            color: #3f2f1b;
        }

        .table thead {
            background-color: #5b4636;
            color: #fff;
        }

        .form-control, .form-select {
            background-color: #fffaf0;
            border: 1px solid #d5c4a1;
        }

        .btn-success {
            background-color: #6c8b5f;
            border-color: #6c8b5f;
        }

        .btn-secondary {
            background-color: #9e8c75;
            border-color: #9e8c75;
        }

        .btn-warning {
            background-color: #d9a441;
            border-color: #d9a441;
        }

        .btn-danger {
            background-color: #c45c4e;
            border-color: #c45c4e;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <ul class="list-group mb-4">
                    <li class="list-group-item active fw-bold">MAIN MENU</li>
                    <a href="dashboard.php" class="list-group-item text-decoration-none">Dashboard</a>
                    <a href="input-barang.php" class="list-group-item text-decoration-none">Inventory</a>
                    <a href="logout.php" class="list-group-item text-decoration-none">Logout</a>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="card vintage-card mb-4">
                    <div class="card-body">
                        <div class="vintage-header">Form Tambah Barang</div>
                        <form id="formBarang" method="POST">
                            <input type="hidden" id="id_barang" name="id_barang">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="nama_barang" class="form-label">Nama Barang</label>
                                    <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="kode_barang" class="form-label">Kode Barang</label>
                                    <input type="text" class="form-control" id="kode_barang" name="kode_barang" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="stock_barang" class="form-label">Stok Barang</label>
                                    <input type="number" class="form-control" id="stock_barang" name="stock_barang" min="0" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="lokasi_barang" class="form-label">Lokasi Barang</label>
                                    <input type="text" class="form-control" id="lokasi_barang" name="lokasi_barang" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="keadaan_barang" class="form-label">Keadaan Barang</label>
                                    <select class="form-select" id="keadaan_barang" name="keadaan_barang" required>
                                        <option value="">Pilih Keadaan</option>
                                        <option value="baik">Baik</option>
                                        <option value="jelek">Jelek</option>
                                        <option value="hilang">Hilang</option>
                                    </select>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="fa fa-plus"></i> Tambah Barang
                                </button>
                                <button type="button" class="btn btn-secondary px-4 ms-2" id="btnKembalikan">
                                    <i class="fa fa-arrow-left"></i> Kembalikan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card vintage-card">
                    <div class="card-body">
                        <div class="vintage-header">Daftar Barang</div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Kode Barang</th>
                                        <th>Stok</th>
                                        <th>Lokasi</th>
                                        <th>Keadaan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="dataBarang">
                                    <?php
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
                                                <button class='btn btn-warning btn-sm btn-edit'
                                                    data-id='" . $row['id'] . "'
                                                    data-nama='" . htmlspecialchars($row['nama_barang']) . "'
                                                    data-kode='" . htmlspecialchars($row['kode_barang']) . "'
                                                    data-stok='" . htmlspecialchars($row['stock_barang']) . "'
                                                    data-lokasi='" . htmlspecialchars($row['lokasi_barang']) . "'
                                                    data-keadaan='" . htmlspecialchars($row['keadaan_barang']) . "'>
                                                    <i class='fa fa-edit'></i>
                                                </button>
                                                <button class='btn btn-danger btn-sm btn-delete' data-id='" . $row['id'] . "'>
                                                    <i class='fa fa-trash'></i>
                                                </button>
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

    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#formBarang').on('submit', function(e) {
                e.preventDefault();
                let url = $('#id_barang').val() === "" ? "cek-data.php" : "update-barang.php";

                $.ajax({
                    url: url,
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.includes("success")) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: $('#id_barang').val() === "" ? 'Barang berhasil ditambahkan.' : 'Barang berhasil diupdate.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $('#formBarang')[0].reset();
                            $('#id_barang').val('');
                            $('.btn-success').html('<i class="fa fa-plus"></i> Tambah Barang');
                            reloadTableBarang();
                        } else {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menyimpan.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Server tidak merespon.', 'error');
                    }
                });
            });

            $(document).on('click', '.btn-edit', function() {
                $('#id_barang').val($(this).data('id'));
                $('#nama_barang').val($(this).data('nama'));
                $('#kode_barang').val($(this).data('kode'));
                $('#stock_barang').val($(this).data('stok'));
                $('#lokasi_barang').val($(this).data('lokasi'));
                $('#keadaan_barang').val($(this).data('keadaan'));
                $('.btn-success').html('<i class="fa fa-save"></i> Update Barang');
            });

            $('#btnKembalikan').click(function() {
                $('#formBarang')[0].reset();
                $('#id_barang').val('');
                $('.btn-success').html('<i class="fa fa-plus"></i> Tambah Barang');
            });

            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'delete-barang.php',
                            type: 'GET',
                            data: { id: id },
                            success: function(response) {
                                if (response.includes("success")) {
                                    Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                                    reloadTableBarang();
                                } else {
                                    Swal.fire('Gagal!', 'Tidak bisa menghapus data.', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'Gagal koneksi ke server.', 'error');
                            }
                        });
                    }
                });
            });

            function reloadTableBarang() {
                $.ajax({
                    url: window.location.href,
                    type: "GET",
                    dataType: "html",
                    success: function(data) {
                        const newTbody = $(data).find("#dataBarang").html();
                        $("#dataBarang").html(newTbody);
                    }
                });
            }
        });
    </script>
</body>

</html>

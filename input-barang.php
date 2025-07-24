<?php
session_start();
if (!$_SESSION['id_user']) {
    header("location: login.php");
    exit;
}
include 'koneksi.php';

// Handle AJAX request untuk load data dengan search dan pagination
if (isset($_POST['ajax_action']) && $_POST['ajax_action'] == 'load_table') {
    $search = isset($_POST['search']) ? mysqli_real_escape_string($connection, $_POST['search']) : '';
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = 5; // Items per page
    $offset = ($page - 1) * $limit;

    // Build search query
    $where_clause = "";
    if (!empty($search)) {
        $where_clause = "WHERE nama_barang LIKE '%$search%' 
                       OR kode_barang LIKE '%$search%' 
                       OR lokasi_barang LIKE '%$search%' 
                       OR keadaan_barang LIKE '%$search%'";
    }

    // Get total records for pagination
    $count_query = "SELECT COUNT(*) as total FROM tbl_barang $where_clause";
    $count_result = mysqli_query($connection, $count_query);
    $total_records = mysqli_fetch_array($count_result)['total'];
    $total_pages = ceil($total_records / $limit);

    // Get data with limit
    $query = "SELECT * FROM tbl_barang $where_clause ORDER BY id DESC LIMIT $limit OFFSET $offset";
    $result = mysqli_query($connection, $query);

    $html = '';
    $no = $offset + 1;

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $html .= "<tr>
                <td>" . $no++ . "</td>
                <td>" . htmlspecialchars($row['nama_barang']) . "</td>
                <td>" . htmlspecialchars($row['kode_barang']) . "</td>
                <td><img src='uploads/" . htmlspecialchars($row['gambar_barang']) . "' width='60' class='img-thumbnail'></td>
                <td>" . htmlspecialchars($row['stock_barang']) . "</td>
                <td>" . htmlspecialchars($row['lokasi_barang']) . "</td>
                <td>" . htmlspecialchars($row['keadaan_barang']) . "</td>
                <td>
                    <div class='btn-group'>
                        <button class='btn btn-warning btn-sm btn-edit' 
                            data-id='" . $row['id'] . "' 
                            data-nama='" . htmlspecialchars($row['nama_barang']) . "' 
                            data-kode='" . htmlspecialchars($row['kode_barang']) . "' 
                            data-stok='" . htmlspecialchars($row['stock_barang']) . "' 
                            data-lokasi='" . htmlspecialchars($row['lokasi_barang']) . "' 
                            data-keadaan='" . htmlspecialchars($row['keadaan_barang']) . "' 
                            data-gambar='" . htmlspecialchars($row['gambar_barang']) . "'>
                            <i class='fa fa-edit'></i> 
                        </button>
                        <button class='btn btn-danger btn-sm btn-delete' data-id='" . $row['id'] . "'>
                            <i class='fa fa-trash'></i> 
                        </button>
                    </div>
                </td>
            </tr>";
        }
    } else {
        $html = "<tr><td colspan='7' class='text-center'>Tidak ada data ditemukan</td></tr>";
    }

    // Return JSON response
    echo json_encode([
        'html' => $html,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'total_records' => $total_records,
        'showing' => mysqli_num_rows($result)
    ]);
    exit;
}

// Handle AJAX request untuk data print
if (isset($_POST['ajax_action']) && $_POST['ajax_action'] == 'get_print_data') {
    $search = isset($_POST['search']) ? mysqli_real_escape_string($connection, $_POST['search']) : '';

    // Build search query
    $where_clause = "";
    if (!empty($search)) {
        $where_clause = "WHERE nama_barang LIKE '%$search%' 
                       OR kode_barang LIKE '%$search%' 
                       OR lokasi_barang LIKE '%$search%' 
                       OR keadaan_barang LIKE '%$search%'";
    }

    // Get all data for print (no pagination)
    $query = "SELECT * FROM tbl_barang $where_clause ORDER BY nama_barang ASC";
    $result = mysqli_query($connection, $query);

    $data = [];
    $no = 1;
    while ($row = mysqli_fetch_array($result)) {
        $data[] = [
            'no' => $no++,
            'nama_barang' => htmlspecialchars($row['nama_barang']),
            'kode_barang' => htmlspecialchars($row['kode_barang']),
            'gambar_barang' => htmlspecialchars($row['gambar_barang']),
            'stock_barang' => htmlspecialchars($row['stock_barang']),
            'lokasi_barang' => htmlspecialchars($row['lokasi_barang']),
            'keadaan_barang' => htmlspecialchars($row['keadaan_barang'])
        ];
    }

    echo json_encode([
        'data' => $data,
        'total' => count($data),
        'search_term' => $search
    ]);
    exit;
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="row">

            <div class="col-md-3">
                <ul class="list-group">
                    <li class="list-group-item list-group-item-primary font-weight-bold">MAIN MENU</li>
                    <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
                    <a href="input-barang.php" class="list-group-item list-group-item-action">Inventory</a>
                    <a href="logout.php" class="list-group-item list-group-item-action">Logout</a>
                </ul>
            </div>

            <div class="col-md-9">
                <div class="card mb-4 shadow-sm ">
                    <div class="card-body">
                        <h5 class="card-title mb-4 list-group-item list-group-item-primary font-weight-bold">Tambah Barang</h5>
                        
                        <form id="formBarang" method="POST">
                            <input type="hidden" id="id_barang" name="id_barang">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="nama_barang">Nama Barang</label>
                                    <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="kode_barang">Kode Barang</label>
                                    <input type="text" class="form-control" id="kode_barang" name="kode_barang" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="gambar_barang">Masukkan gambar</label>
                                    <input type="file" class="form-control-file" id="gambar_barang" name="gambar_barang" required>
                                    <input type="hidden" name="gambar_barang_hidden" id="gambar_barang_hidden">
                                    <div id="previewGambar" class="mt-2"></div>
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

                                <button type="button" class="btn btn-secondary px-4 ml-2" id="btnKembalikan">
                                    <i class="fa fa-arrow-left"></i> Kembalikan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="mb-0">List Barang</h2>
                        </div>
                        <hr>

                        <!-- Action Buttons -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <!-- Search Box -->
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Cari nama, kode, lokasi, atau keadaan barang...">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="btn-group float-right">
                                    <button class="btn btn-info" id="btnPrint">
                                        <i class="fa fa-print"></i> Print Data
                                    </button>
                                    <button class="btn btn-success" id="btnPrintAll">
                                        <i class="fa fa-print"></i> Print Semua
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="tabelBarang">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Kode Barang</th>
                                        <th>Gambar</th>
                                        <th>Stok Barang</th>
                                        <th>Lokasi Barang</th>
                                        <th>Keadaan Barang</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="dataBarang">
                                    <!-- Data akan dimuat via AJAX -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Info and Controls -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted small" id="tableInfo">
                                Menampilkan 0 dari 0 data
                            </div>
                            <nav>
                                <ul class="pagination pagination-sm mb-0" id="pagination">
                                    <!-- Pagination akan dibuat via JavaScript -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Area (Hidden) -->
    <div class="d-none" id="printArea">
        <div class="text-center mb-4 border-bottom pb-3">
            <h2 class="h3 font-weight-bold">LAPORAN DATA INVENTORY BARANG</h2>
            <p class="mb-1" id="printSubtitle">Semua Data Barang</p>
            <p class="mb-0">Tanggal Cetak: <span id="printDate"></span></p>
        </div>

        <table class="table table-bordered table-sm" id="printTable">
            <thead class="thead-light">
                <tr>
                    <th class="text-center" style="width: 5%;">No</th>
                    <th style="width: 25%;">Nama Barang</th>
                    <th style="width: 15%;">Kode Barang</th>
                    <th class="text-center" style="width: 10%;">Stok</th>
                    <th style="width: 25%;">Lokasi</th>
                    <th class="text-center" style="width: 20%;">Keadaan</th>
                </tr>
            </thead>
            <tbody id="printTableBody">
                <!-- Data akan diisi via JavaScript -->
            </tbody>
        </table>

        <div class="text-right mt-4 small">
            <p class="mb-1">Total Data: <span id="printTotal" class="font-weight-bold">0</span> item</p>
            <p class="mb-0">Dicetak pada: <span id="printTimestamp"></span></p>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            let currentPage = 1;
            let currentSearch = '';

            // Load data saat halaman pertama kali dimuat
            loadTableData();

            // Search functionality dengan debounce
            let searchTimeout;
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    currentSearch = $('#searchInput').val().trim();
                    currentPage = 1;
                    loadTableData();
                }, 500); // Delay 500ms
            });

            // Clear search
            $('#clearSearch').click(function() {
                $('#searchInput').val('');
                currentSearch = '';
                currentPage = 1;
                loadTableData();
            });

            // Function untuk load data table
            function loadTableData() {
                // Show loading
                $('#dataBarang').html('<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fa fa-spinner fa-spin mr-2"></i>Memuat data...</td></tr>');

                $.ajax({
                    url: window.location.href,
                    type: 'POST',
                    data: {
                        ajax_action: 'load_table',
                        search: currentSearch,
                        page: currentPage
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Update table content
                        $('#dataBarang').html(response.html);

                        // Update pagination
                        generatePagination(response.total_pages, response.current_page);

                        // Update info
                        $('#tableInfo').text(`Menampilkan ${response.showing} dari ${response.total_records} data`);
                    },
                    error: function() {
                        $('#dataBarang').html('<tr><td colspan="7" class="text-center text-danger py-4">Error memuat data</td></tr>');
                    }
                });
            }

            // Function untuk generate pagination
            function generatePagination(totalPages, currentPageNum) {
                let paginationHtml = '';

                if (totalPages > 1) {
                    // Previous button
                    if (currentPageNum > 1) {
                        paginationHtml += `<li class="page-item">
                            <a class="page-link" href="#" data-page="${currentPageNum - 1}">Previous</a>
                        </li>`;
                    }

                    // Page numbers
                    let startPage = Math.max(1, currentPageNum - 2);
                    let endPage = Math.min(totalPages, currentPageNum + 2);

                    // First page
                    if (startPage > 1) {
                        paginationHtml += `<li class="page-item">
                            <a class="page-link" href="#" data-page="1">1</a>
                        </li>`;
                        if (startPage > 2) {
                            paginationHtml += `<li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>`;
                        }
                    }

                    // Page numbers around current page
                    for (let i = startPage; i <= endPage; i++) {
                        let activeClass = i === currentPageNum ? 'active' : '';
                        paginationHtml += `<li class="page-item ${activeClass}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>`;
                    }

                    // Last page
                    if (endPage < totalPages) {
                        if (endPage < totalPages - 1) {
                            paginationHtml += `<li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>`;
                        }
                        paginationHtml += `<li class="page-item">
                            <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                        </li>`;
                    }

                    // Next button
                    if (currentPageNum < totalPages) {
                        paginationHtml += `<li class="page-item">
                            <a class="page-link" href="#" data-page="${currentPageNum + 1}">Next</a>
                        </li>`;
                    }
                }

                $('#pagination').html(paginationHtml);
            }

            // Pagination click handler
            $(document).on('click', '.page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page !== currentPage) {
                    currentPage = page;
                    loadTableData();
                }
            });
            $('#gambar_barang').on('change', function() {
                const fileInput = this;
                const formData = new FormData();
                formData.append('gambar_barang', fileInput.files[0]);

                // Tampilkan loading
                $('#previewGambar').html('<i class="fa fa-spinner fa-spin"></i> Mengupload gambar...');

                $.ajax({
                    url: 'upload-gambar.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#gambar_barang_hidden').val(response.file);
                            $('#previewGambar').html(`<img src="uploads/${response.file}" class="img-thumbnail" width="120">`);
                        } else {
                            $('#previewGambar').html(`<span class="text-danger">${response.message}</span>`);
                        }
                    },
                    error: function() {
                        $('#previewGambar').html('<span class="text-danger">Gagal mengupload gambar.</span>');
                    }
                });
            });

            // Print Functions
            function formatDate(date) {
                const options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                return date.toLocaleDateString('id-ID', options);
            }

            function generatePrintData(data, searchTerm = '') {
                let tableBody = '';
                data.forEach(function(item, index) {
                    tableBody += `
                        <tr>
                            <td class="text-center">${item.no}</td>
                            <td>${item.nama_barang}</td>
                            <td>${item.kode_barang}</td>
                            <td class="text-center">${item.stock_barang}</td>
                            <td>${item.lokasi_barang}</td>
                            <td class="text-center">
                                <span class="badge badge-${item.keadaan_barang === 'baik' ? 'success' : item.keadaan_barang === 'jelek' ? 'warning' : 'danger'}">
                                    ${item.keadaan_barang.toUpperCase()}
                                </span>
                            </td>
                        </tr>
                    `;
                });

                $('#printTableBody').html(tableBody);
                $('#printTotal').text(data.length);

                // Set print subtitle
                if (searchTerm) {
                    $('#printSubtitle').text(`Data Hasil Pencarian: "${searchTerm}"`);
                } else {
                    $('#printSubtitle').text('Semua Data Barang');
                }

                // Set dates
                const now = new Date();
                $('#printDate').text(now.toLocaleDateString('id-ID'));
                $('#printTimestamp').text(formatDate(now));
            }

            function openPrintWindow(content) {
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Print Laporan Inventory</title>
                        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
                        <style>
                            @media print {
                                body { margin: 0; }
                                .no-print { display: none !important; }
                            }
                            body { font-family: Arial, sans-serif; }
                        </style>
                    </head>
                    <body class="p-4">
                        ${content}
                        <div class="text-center mt-4 no-print">
                            <button onclick="window.print()" class="btn btn-primary mr-2">
                                <i class="fa fa-print"></i> Print
                            </button>
                            <button onclick="window.close()" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Tutup
                            </button>
                        </div>
                    </body>
                    </html>
                `);
                printWindow.document.close();
            }

            // Print filtered data (berdasarkan pencarian saat ini)
            $('#btnPrint').click(function() {
                const button = $(this);
                button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Loading...');

                $.ajax({
                    url: window.location.href,
                    type: 'POST',
                    data: {
                        ajax_action: 'get_print_data',
                        search: currentSearch
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.data.length > 0) {
                            generatePrintData(response.data, response.search_term);
                            openPrintWindow($('#printArea').html());
                        } else {
                            Swal.fire('Info', 'Tidak ada data untuk dicetak.', 'info');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal memuat data untuk print.', 'error');
                    },
                    complete: function() {
                        button.prop('disabled', false).html('<i class="fa fa-print"></i> Print Data');
                    }
                });
            });

            // Print all data (tanpa filter pencarian)
            $('#btnPrintAll').click(function() {
                const button = $(this);
                button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Loading...');

                $.ajax({
                    url: window.location.href,
                    type: 'POST',
                    data: {
                        ajax_action: 'get_print_data',
                        search: '' // Kosong untuk semua data
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.data.length > 0) {
                            generatePrintData(response.data, '');
                            openPrintWindow($('#printArea').html());
                        } else {
                            Swal.fire('Info', 'Tidak ada data untuk dicetak.', 'info');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal memuat data untuk print.', 'error');
                    },
                    complete: function() {
                        button.prop('disabled', false).html('<i class="fa fa-print"></i> Print Semua');
                    }
                });
            });

            // Simpan & Update Barang
            $('#formBarang').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                let url = $('#id_barang').val() === "" ? "cek-data.php" : "update-barang.php";

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
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
                            $('#previewGambar').html('');
                            $('#id_barang').val('');
                            $('#formBarang button[type="submit"]').html('<i class="fa fa-plus"></i> Tambah Barang');
                            loadTableData();
                        } else {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menyimpan.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Server tidak merespon.', 'error');
                    }
                });
            });


            // Tombol Edit
            // Ganti bagian JavaScript untuk edit dan submit form dengan kode ini:

            // Tombol Edit - DIPERBAIKI
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const kode = $(this).data('kode');
                const stok = $(this).data('stok');
                const lokasi = $(this).data('lokasi');
                const keadaan = $(this).data('keadaan');
                const gambar = $(this).data('gambar');

                // Isi form dengan data
                $('#id_barang').val(id);
                $('#nama_barang').val(nama);
                $('#kode_barang').val(kode);
                $('#stock_barang').val(stok);
                $('#lokasi_barang').val(lokasi);
                $('#keadaan_barang').val(keadaan);

                // Handle gambar
                if (gambar && gambar !== '') {
                    $('#previewGambar').html(`<img src="uploads/${gambar}" class="img-thumbnail" width="120">`);
                    $('#gambar_barang_hidden').val(gambar);
                } else {
                    $('#previewGambar').html('');
                    $('#gambar_barang_hidden').val('');
                }

                // Reset file input
                $('#gambar_barang').val('');

                // Ubah tombol submit
                $('#formBarang button[type="submit"]').html('<i class="fa fa-save"></i> Update Barang');

                // Scroll ke form
                $('html, body').animate({
                    scrollTop: $("#formBarang").offset().top - 20
                }, 500);
            });

            // Submit Form - DIPERBAIKI
            $('#formBarang').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                let isEdit = $('#id_barang').val() !== "";
                let url = isEdit ? "update-barang.php" : "cek-data.php";

                // Disable tombol submit
                let submitBtn = $('#formBarang button[type="submit"]');
                let originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log('Response:', response); // Debug

                        if (response.includes("success")) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: isEdit ? 'Barang berhasil diupdate.' : 'Barang berhasil ditambahkan.',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Reset form
                            $('#formBarang')[0].reset();
                            $('#previewGambar').html('');
                            $('#id_barang').val('');
                            $('#gambar_barang_hidden').val('');
                            submitBtn.html('<i class="fa fa-plus"></i> Tambah Barang');

                            // Reload table
                            loadTableData();
                        } else {
                            // Handle different error types
                            let errorMessage = 'Terjadi kesalahan saat menyimpan.';

                            if (response.includes("error_empty_fields")) {
                                errorMessage = 'Semua field harus diisi.';
                            } else if (response.includes("error_duplicate_code")) {
                                errorMessage = 'Kode barang sudah ada.';
                            } else if (response.includes("error_no_image")) {
                                errorMessage = 'Gambar harus dipilih.';
                            } else if (response.includes("error_upload_failed")) {
                                errorMessage = 'Gagal mengupload gambar.';
                            } else if (response.includes("error_invalid_file")) {
                                errorMessage = 'Format file tidak valid.';
                            } else if (response.includes("error_database")) {
                                errorMessage = 'Error database: ' + response.split(': ')[1];
                            }

                            Swal.fire('Gagal!', errorMessage, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Ajax Error:', error);
                        Swal.fire('Error!', 'Server tidak merespon: ' + error, 'error');
                    },
                    complete: function() {
                        // Re-enable tombol submit
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Upload gambar saat edit - DIPERBAIKI
            $('#gambar_barang').on('change', function() {
                const fileInput = this;

                if (fileInput.files && fileInput.files[0]) {
                    const formData = new FormData();
                    formData.append('gambar_barang', fileInput.files[0]);

                    // Tampilkan loading
                    $('#previewGambar').html('<i class="fa fa-spinner fa-spin"></i> Mengupload gambar...');

                    $.ajax({
                        url: 'upload-gambar.php',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                $('#gambar_barang_hidden').val(response.file);
                                $('#previewGambar').html(`<img src="uploads/${response.file}" class="img-thumbnail" width="120">`);
                            } else {
                                $('#previewGambar').html(`<span class="text-danger">${response.message}</span>`);
                                $('#gambar_barang_hidden').val('');
                            }
                        },
                        error: function() {
                            $('#previewGambar').html('<span class="text-danger">Gagal mengupload gambar.</span>');
                            $('#gambar_barang_hidden').val('');
                        }
                    });
                }
            });

            // Tombol Kembalikan - DIPERBAIKI
            $('#btnKembalikan').click(function() {
                $('#formBarang')[0].reset();
                $('#id_barang').val('');
                $('#gambar_barang_hidden').val('');
                $('#previewGambar').html('');
                $('#formBarang button[type="submit"]').html('<i class="fa fa-plus"></i> Tambah Barang');
            });

            // Tombol Delete
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'delete-barang.php',
                            type: 'GET',
                            data: {
                                id: id
                            },
                            success: function(response) {
                                if (response.includes("success")) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: 'Data berhasil dihapus.',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    loadTableData();
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
        });
    </script>
</body>

</html>
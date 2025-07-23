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
    $limit = 10; // Items per page
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
                            data-keadaan='" . htmlspecialchars($row['keadaan_barang']) . "'>
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

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <style>
        .search-wrapper {
            margin-bottom: 20px;
        }
        
        .pagination-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }
        
        .table-info {
            color: #6c757d;
            font-size: 0.9em;
        }
        
        .pagination .page-link {
            color: #007bff;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }
    </style>
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
                        <h5 class="card-title mb-4">Tambah Barang</h5> <hr>
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
                        
                        <!-- Search Box -->
                        <div class="search-wrapper">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="searchInput" placeholder="Cari nama, kode, lokasi, atau keadaan barang...">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
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
                        <div class="pagination-info">
                            <div class="table-info" id="tableInfo">
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
                $('#dataBarang').html('<tr><td colspan="7" class="loading"><i class="fa fa-spinner fa-spin"></i> Memuat data...</td></tr>');
                
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
                        $('#dataBarang').html('<tr><td colspan="7" class="text-center text-danger">Error memuat data</td></tr>');
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
            
            // Sisanya tetap sama seperti kode asli Anda
            
            // Simpan & Update Barang
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
                            // Reload table data instead of reloadTableBarang()
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

            // Tombol Delete
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
                            data: {
                                id: id
                            },
                            success: function(response) {
                                if (response.includes("success")) {
                                    Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                                    // Reload table data instead of reloadTableBarang()
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
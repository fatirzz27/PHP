<?php
session_start();
if (!$_SESSION['id_user']) {
    header("location: login.php");
    exit;
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $id_barang = mysqli_real_escape_string($connection, $_POST['id_barang']);
    $nama_barang = mysqli_real_escape_string($connection, $_POST['nama_barang']);
    $kode_barang = mysqli_real_escape_string($connection, $_POST['kode_barang']);
    $stock_barang = mysqli_real_escape_string($connection, $_POST['stock_barang']);
    $lokasi_barang = mysqli_real_escape_string($connection, $_POST['lokasi_barang']);
    $keadaan_barang = mysqli_real_escape_string($connection, $_POST['keadaan_barang']);
    
    // Validasi input
    if (empty($id_barang) || empty($nama_barang) || empty($kode_barang) || empty($stock_barang) || empty($lokasi_barang) || empty($keadaan_barang)) {
        echo "error_empty_fields";
        exit;
    }
    
    // Cek apakah ada gambar baru yang diupload
    $gambar_barang = '';
    
    // Jika ada file gambar baru yang diupload
    if (isset($_FILES['gambar_barang']) && $_FILES['gambar_barang']['error'] == 0) {
        $target_dir = "uploads/";
        $file_extension = strtolower(pathinfo($_FILES['gambar_barang']['name'], PATHINFO_EXTENSION));
        
        // Validasi ekstensi file
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        if (!in_array($file_extension, $allowed_extensions)) {
            echo "error_invalid_file";
            exit;
        }
        
        // Generate nama file unik
        $new_filename = time() . "_" . uniqid() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Upload file
        if (move_uploaded_file($_FILES['gambar_barang']['tmp_name'], $target_file)) {
            $gambar_barang = $new_filename;
            
            // Hapus gambar lama jika ada
            $query_old = "SELECT gambar_barang FROM tbl_barang WHERE id = '$id_barang'";
            $result_old = mysqli_query($connection, $query_old);
            if ($result_old && mysqli_num_rows($result_old) > 0) {
                $old_data = mysqli_fetch_array($result_old);
                $old_image = $old_data['gambar_barang'];
                if (!empty($old_image) && file_exists($target_dir . $old_image)) {
                    unlink($target_dir . $old_image);
                }
            }
        } else {
            echo "error_upload_failed";
            exit;
        }
    } 
    // Jika tidak ada file baru, gunakan gambar yang sudah ada (dari hidden field)
    else if (isset($_POST['gambar_barang_hidden']) && !empty($_POST['gambar_barang_hidden'])) {
        $gambar_barang = mysqli_real_escape_string($connection, $_POST['gambar_barang_hidden']);
    } 
    // Jika tidak ada gambar sama sekali
    else {
        echo "error_no_image";
        exit;
    }
    
    // Cek duplikasi kode barang (kecuali untuk data yang sedang diedit)
    $check_query = "SELECT id FROM tbl_barang WHERE kode_barang = '$kode_barang' AND id != '$id_barang'";
    $check_result = mysqli_query($connection, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo "error_duplicate_code";
        exit;
    }
    
    // Update data ke database
    $update_query = "UPDATE tbl_barang SET 
                     nama_barang = '$nama_barang',
                     kode_barang = '$kode_barang',
                     gambar_barang = '$gambar_barang',
                     stock_barang = '$stock_barang',
                     lokasi_barang = '$lokasi_barang',
                     keadaan_barang = '$keadaan_barang'
                     WHERE id = '$id_barang'";
    
    if (mysqli_query($connection, $update_query)) {
        echo "success";
    } else {
        echo "error_database: " . mysqli_error($connection);
    }
} else {
    echo "error_method";
}
?>
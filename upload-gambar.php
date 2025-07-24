<?php
session_start();
if (!$_SESSION['id_user']) {
    header("Content-Type: application/json");
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['gambar_barang'])) {
    $target_dir = "uploads/";
    
    // Pastikan folder uploads ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    $file = $_FILES['gambar_barang'];
    
    // Validasi error upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'Error saat upload file']);
        exit;
    }
    
    // Validasi ukuran file (maksimal 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $max_size) {
        echo json_encode(['status' => 'error', 'message' => 'Ukuran file terlalu besar (maksimal 5MB)']);
        exit;
    }
    
    // Validasi tipe file
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($file['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(['status' => 'error', 'message' => 'Tipe file tidak diizinkan. Gunakan JPG, PNG, atau GIF']);
        exit;
    }
    
    // Generate nama file unik
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $new_filename = time() . "_" . uniqid() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Pindahkan file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Gambar berhasil diupload',
            'file' => $new_filename
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Tidak ada file yang diupload']);
}
?>
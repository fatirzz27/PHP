<?php
include 'koneksi.php';


$id = $_GET['id'];

$query = "delete from tbl_barang where id = '$id'";

if(mysqli_query($connection, $query)) {
    header("location: input-barang.php?success=delete");
} else {
    header("location: input-barang.php?error=db");
}               

mysqli_close($connection);
?>
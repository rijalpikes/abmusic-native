<?php
$koneksi = new mysqli("localhost", "root", "", "abmusic_db");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>
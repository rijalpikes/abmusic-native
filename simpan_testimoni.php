<?php
// Koneksi ke database
$host = "localhost";
$dbname = "abmusic_db"; // ganti sesuai nama database kamu
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Ambil data dari form
$nama = $_POST['nama'];
$no_hp = $_POST['no_hp'];
$pesan = $_POST['pesan'];
$rating = $_POST['rating'];
$tanggal = date('Y-m-d'); // Tanggal saat ini
// var_dump($pesan); // Debugging: tampilkan data yang diterima
// die();
// Validasi sederhana (opsional bisa ditambahkan lebih banyak)
if (empty($nama) || empty($no_hp) || empty($pesan) || empty($rating)) {
    echo "<script>alert('Semua field wajib diisi!');history.back();</script>";
    exit;
}

// Simpan dengan prepared statement
$sql = "INSERT INTO testimoni (nama, pesan, tanggal, rating)
        VALUES (:nama, :pesan, :tanggal, :rating)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':nama' => htmlspecialchars($nama),
    ':pesan' => htmlspecialchars($pesan),
    ':tanggal' => date('Y-m-d'),
    ':rating' => htmlspecialchars($rating),
]);

echo "<script>alert('Testimoni berhasil dikirim!');window.location.href='index.php';</script>";
?>
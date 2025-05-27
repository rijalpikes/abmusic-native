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
$tanggal_pesan = $_POST['tanggal_pesan'];
$keterangan = $_POST['keterangan'];

// Validasi sederhana (opsional bisa ditambahkan lebih banyak)
if (empty($nama) || empty($no_hp) || empty($tanggal_pesan)) {
    echo "<script>alert('Semua field wajib diisi!');history.back();</script>";
    exit;
}

// Simpan dengan prepared statement
$sql = "INSERT INTO pemesanan (nama, no_hp, tanggal_pesan, keterangan)
        VALUES (:nama, :no_hp, :tanggal_pesan, :keterangan)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':nama' => htmlspecialchars($nama),
    ':no_hp' => htmlspecialchars($no_hp),
    ':tanggal_pesan' => $tanggal_pesan,
    ':keterangan' => htmlspecialchars($keterangan)
]);

echo "<script>alert('Pemesanan berhasil dikirim!');window.location.href='index.php';</script>";
?>
<?php
include '../config/database.php';
$pdo = connect_db(); // Inisialisasi koneksi ke database
// Ambil data dari form
$nama = $_POST['nama'];
$no_hp = $_POST['no_hp'];
$pesan = $_POST['pesan'];
$rating = $_POST['rating'];
$pemesanan_id = $_POST['pemesanan_id'];
$tanggal = date('Y-m-d'); // Tanggal saat ini
// var_dump($pesan); // Debugging: tampilkan data yang diterima
// die();
// Validasi sederhana (opsional bisa ditambahkan lebih banyak)
if (empty($nama) || empty($no_hp) || empty($pesan) || empty($rating)) {
    echo "<script>alert('Semua field wajib diisi!');history.back();</script>";
    exit;
}
// Ambil protocol
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Ambil domain (host)
$domain = $_SERVER['HTTP_HOST'];

// Bentuk base URL
$base_url = $protocol . $domain;
// Kalau SUDAH testimoni
$pesan = "Hayyy {$nama}, terima kasih sudah memberikan testimoni untuk ABmusic! 🙏✨\n\n"
    . "Kami sangat menghargai dukunganmu. Semoga layanan kami selalu memuaskan ❤️\n\n"
    . "Website : {$base_url}\n\n"
    . "Salam hangat,\nABmusic Team";

// Format nomor WA (pastikan sudah 62xxxx)
$nomorWA = preg_replace('/^0/', '62', $no_hp) . '@c.us';

$data = [
    "chatId" => $nomorWA,
    "reply_to" => null,
    "text" => $pesan,
    "linkPreview" => true,
    "session" => "081355071767" // Sesuaikan session WA kamu
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://rijalpikes-wabot.6f4u4c.easypanel.host/api/sendText',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_UNICODE),
    CURLOPT_HTTPHEADER => [
        'X-Api-Key: 321',
        'Content-Type: application/json'
    ],
]);
$response = curl_exec($curl);
curl_close($curl);


// Simpan dengan prepared statement
$sql = "INSERT INTO testimoni (nama, pesan, tanggal, rating, no_hp, pemesanan_id)
        VALUES (:nama, :pesan, :tanggal, :rating, :no_hp, :pemesanan_id)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':nama' => htmlspecialchars($nama),
    ':pesan' => htmlspecialchars($pesan),
    ':tanggal' => date('Y-m-d'),
    ':rating' => htmlspecialchars($rating),
    ':no_hp' => htmlspecialchars($no_hp),
    ':pemesanan_id' => htmlspecialchars($pemesanan_id),
]);
// Ambil protocol
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Ambil domain (host)
$domain = $_SERVER['HTTP_HOST'];

// Bentuk base URL
$base_url = $protocol . $domain;
echo "<script>alert('Testimoni berhasil dikirim!');window.location.href='$base_url';</script>";

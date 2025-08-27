<?php
include 'config/database.php';
include 'includes/fungsi.php';
$pdo = connect_db(); // Inisialisasi koneksi ke database

// Ambil dan sanitasi data dari form
$nama           = trim($_POST['nama']);
$no_hp          = trim($_POST['no_hp']);
$tanggal_pesan  = $_POST['tanggal_pesan'];
$keterangan     = trim($_POST['keterangan']);
$paket          = trim($_POST['paket']);
$tarif          = trim($_POST['harga']);
$lokasi         = $_POST['lokasi'];

// Validasi input sederhana
if (empty($nama) || empty($no_hp) || empty($tanggal_pesan)) {
    echo "<script>alert('Semua field wajib diisi!');history.back();</script>";
    exit;
}

// Validasi tanggal pesan tidak boleh kurang dari hari ini
$today = date('Y-m-d');
if ($tanggal_pesan < $today) {
    echo "<script>alert('Tanggal pemesanan tidak boleh kurang dari hari ini!');history.back();</script>";
    exit;
}

// Format nomor WA ke format internasional (ganti 08xx jadi 628xx)
$nomorWA = preg_replace('/[^0-9]/', '', $no_hp); // hanya angka
if (substr($nomorWA, 0, 1) === '0') {
    $nomorWA = '62' . substr($nomorWA, 1) . '@c.us';
}

// Format tarif ke Rupiah
$tarif_rupiah = "Rp " . number_format($tarif, 0, ',', '.');
$kode = substr(md5(time() . rand()), 0, 8); // contoh: ab12cd34
// URL form pembayaran (misalnya halaman pembayaran.php dengan query string)

// Ambil protocol
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Ambil domain (host)
$domain = $_SERVER['HTTP_HOST'];

// Bentuk base URL
$base_url = $protocol . $domain;

$url_pembayaran = $base_url . "/pay/index.php?kode=" . $kode;
$tanggal_indonesia = formatTanggalIndo($tanggal_pesan);
// Format pesan WhatsApp
$pesan = "*Welcome to ABmusic..🙏🏻

Hayyy $nama*, terima kasih telah memesan layanan *ABmusic*! 🎤

📦 *Paket*: $paket
📅 *Tanggal*: $tanggal_indonesia
📞 *No HP*: $no_hp
📍 *Lokasi*: $lokasi
💰 *Tarif*: $tarif_rupiah
📝 *Keterangan*: $keterangan

Silakan lakukan pembayaran melalui link berikut:
👉 $url_pembayaran

Kami akan segera menghubungi Anda untuk konfirmasi lebih lanjut.

Salam hangat,  
*ABmusic Team*";

// Kirim WhatsApp lewat Waha
$data = [
    "chatId" => $nomorWA, // contoh: 628123456789@c.us
    "reply_to" => null,
    "text" => $pesan,
    "linkPreview" => true,
    "linkPreviewHighQuality" => false,
    "session" => "081355071767" // Ganti dengan nama session kamu di Waha
];
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://rijalpikes-wabot.6f4u4c.easypanel.host/api/sendText',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_UNICODE), // ini penting untuk emoji
    CURLOPT_HTTPHEADER => array(
        'X-Api-Key: 321',
        'Content-Type: application/json'
    ),
));

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$error = curl_error($curl);
curl_close($curl);

// Cek hasil kirim WA
if ($httpCode !== 201) {
    echo "<script>alert('Pemesanan gagal mengirim WhatsApp! Silakan coba lagi.');history.back();</script>";
    exit;
}

// Simpan ke database (prepared statement untuk keamanan)
try {
    $sql = "INSERT INTO pemesanan (nama, no_hp, lokasi, tanggal_pesan, keterangan, paket, tarif, kode_bayar)
            VALUES (:nama, :no_hp, :lokasi, :tanggal_pesan, :keterangan, :paket, :tarif, :kode)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nama' => htmlspecialchars($nama),
        ':no_hp' => htmlspecialchars($no_hp),
        ':lokasi' => htmlspecialchars($lokasi),
        ':tanggal_pesan' => $tanggal_pesan,
        ':keterangan' => htmlspecialchars($keterangan),
        ':paket' => htmlspecialchars($paket),
        ':tarif' => htmlspecialchars($tarif),
        ':kode' => htmlspecialchars($kode)
    ]);

    echo "<script>alert('Pemesanan berhasil dikirim!');window.location.href='index.php';</script>";
} catch (PDOException $e) {
    echo "<script>alert('Gagal menyimpan ke database: " . $e->getMessage() . "');history.back();</script>";
}

<?php
require_once __DIR__ . '/../midtrans/Midtrans.php';
include "../config/database.php";
include "../includes/fungsi.php";
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Access denied: Only POST allowed.');
}

require_once '../midtrans_config.php';

$notif = new \Midtrans\Notification();

$order_id = $notif->order_id ?? null;
$transaction = $notif->transaction_status ?? null;
$status_code = $notif->status_code;
$gross_amount = $notif->gross_amount;

if (!$order_id) {
    exit('No order_id received.');
}
$pdo = connect_db(); // Inisialisasi koneksi ke database
// Ambil detail pembayaran di DB
$stmt = $pdo->prepare("SELECT p.*, pm.nama, pm.no_hp, pk.nama_paket, pm.tanggal_pesan
                       FROM pembayaran p
                       JOIN pemesanan pm ON pm.id = p.pemesanan_id
                       JOIN paket pk ON pk.id = pm.paket
                       WHERE p.order_id = :order_id LIMIT 1");
$stmt->execute([':order_id' => $order_id]);
$pembayaran = $stmt->fetch(PDO::FETCH_ASSOC);
// koneksi DB
$pdo = connect_db();
if ($transaction === 'settlement' || $transaction == 'capture') {
    $stmt = $pdo->prepare("UPDATE pembayaran SET status='success' WHERE order_id=:order_id");
    $stmt->execute([':order_id' => $order_id]);
} elseif ($transaction === 'pending') {
    $stmt = $pdo->prepare("UPDATE pembayaran SET status='pending' WHERE order_id=:order_id");
    $stmt->execute([':order_id' => $order_id]);
} else {
    $stmt = $pdo->prepare("UPDATE pembayaran SET status='failed' WHERE order_id=:order_id");
    $stmt->execute([':order_id' => $order_id]);
}

if ($transaction == 'settlement') {

    $nomorWA = preg_replace('/^0/', '62', $pembayaran['no_hp']) . '@c.us';
    echo "$nomorWA";
    $stmt = $pdo->prepare("SELECT p.*, pm.nama, pm.no_hp, pk.nama_paket, pm.tanggal_pesan, pm.tarif, p.jenis, sum(nominal) AS total_nominal, kode_bayar,keterangan
                               FROM pemesanan pm
                               JOIN pembayaran p ON p.pemesanan_id = pm.id
                               JOIN paket pk ON pm.paket = pk.id
                               WHERE p.pemesanan_id = :pemesanan_id");
    $stmt->execute([':pemesanan_id' => $pembayaran['pemesanan_id']]);
    $pemesanan = $stmt->fetch(PDO::FETCH_ASSOC);


    $nominal_indonesia = number_format($pemesanan['total_nominal']);
    $tarif_indonesia = number_format($pemesanan['tarif']);
    $sisa_indonesia = number_format($pemesanan['tarif'] - $pemesanan['total_nominal']);
    $tanggal_indonesia = formatTanggalIndo($pembayaran['tanggal_pesan']);

    // Ambil protocol
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
        || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

    // Ambil domain (host)
    $domain = $_SERVER['HTTP_HOST'];

    // Bentuk base URL
    $base_url = $protocol . $domain;

    $url_pembayaran = $base_url . "/pay/index.php?kode=" . $pemesanan['kode_bayar'];
    $url_testimoni = $base_url . "/testimoni/index.php?kode=" . $pemesanan['kode_bayar'];


    if ($pemesanan['tarif'] == $pemesanan['total_nominal']) {
        $pesan = "Hayyy $pemesanan[nama], terima kasih telah melakukan pembayaran tunai untuk ABmusic! 🎤\n\n"
            . "📦 Paket: $pemesanan[nama_paket]\n"
            . "📅 Tanggal: $tanggal_indonesia\n"
            . "📞 No HP: $pemesanan[no_hp]\n"
            . "💰 Tarif: Rp $tarif_indonesia\n"
            . "📝 Keterangan: $pemesanan[keterangan]\n\n"
            . "✅ Status: Lunas (Tunai)\n\n"
            . "👉 $url_testimoni.\n\n"
            . "Salam hangat,\nABmusic Team";
    } else {
        $pesan = "Hayyy $pemesanan[nama], pembayaran tunai untuk ABmusic masih *BELUM LUNAS* ⚠️\n\n"
            . "📦 Paket: $pemesanan[nama_paket]\n"
            . "📅 Tanggal: $tanggal_indonesia\n"
            . "📞 No HP: $pemesanan[no_hp]\n"
            . "💰 Tarif: Rp $tarif_indonesia\n"
            . "💰 Panjar: Rp $nominal_indonesia\n"
            . "💰 Sisa Pembayaran: Rp $sisa_indonesia\n"
            . "📝 Keterangan: $pemesanan[keterangan]\n\n"
            . "❌ Status: Belum Lunas\n"
            . "Silakan melunasi sisa pembayaran sebelum hari-H.\n"
            . "👉 $url_pembayaran.\n\n"
            . "Salam hangat,\nABmusic Team";
    }
    echo "$pesan";
    $data = [
        "chatId" => $nomorWA,
        "reply_to" => null,
        "text" => $pesan,
        "linkPreview" => true,
        "session" => "081355071767" // Sesuaikan session WA kamu
    ];
    // Kirim via API WA Gateway (contoh: Fonnte)
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
}

http_response_code(200);
// optional: kirim response ke Midtrans
echo json_encode(['status' => 'ok', 'okok' => $transaction]);

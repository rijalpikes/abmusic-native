<?php
include "../config/database.php";
require_once __DIR__ . '/../midtrans/Midtrans.php';


$pdo = connect_db(); // Inisialisasi koneksi ke database
// Ambil data dari form
$pemesanan_id = $_POST['pemesanan_id'] ?? 0;
$nama   = $_POST['nama'] ?? '';
$nama_paket  = $_POST['nama_paket'] ?? '';
$tarif  = $_POST['tarif'] ?? 0;
$jenis  = $_POST['jenis'] ?? '';
$nominal = $_POST['nominal'] ?? 0;
$no_hp = $_POST['no_hp'] ?? 0;
$tanggal_pesan = $_POST['tanggal_pesan'] ?? 0;
$total_bayar = $_POST['total_bayar'] ?? 0;
$sisa_tagihan = $_POST['sisa_tagihan'] ?? 0;
$keterangan = $_POST['keterangan'] ?? 0;
$order_id = 'ORDER_' . uniqid();


// Ambil protocol
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Ambil domain (host)
$domain = $_SERVER['HTTP_HOST'];

// Format nomor WA ke format internasional (ganti 08xx jadi 628xx)
$nomorWA = preg_replace('/[^0-9]/', '', $no_hp); // hanya angka
if (substr($nomorWA, 0, 1) === '0') {
    $nomorWA = '62' . substr($nomorWA, 1) . '@c.us';
}
// Bentuk base URL
$base_url = $protocol . $domain;

// Jika jenis tunai langsung simpan
if ($jenis == 'tunai') {
    try {
        $sql = "INSERT INTO pembayaran (pemesanan_id, nominal, jenis, status, order_id, created_at)
                VALUES (:pemesanan_id, :nominal, :jenis, 'success', :order_id,  NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':pemesanan_id' => $pemesanan_id,
            ':nominal' => $nominal,
            ':order_id' => $order_id,
            ':jenis' => $jenis
        ]);

        echo "<script>
            window.location.href='success.php?order_id=' + " . json_encode($order_id) . " + '&jenis=" . $jenis . "';
        </script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    require_once '../midtrans_config.php';

    $params = [
        'transaction_details' => [
            'order_id' => $order_id,
            'gross_amount' => (int)$nominal,
        ],
        'customer_details' => [
            'first_name' => $nama,
            'email' => 'customer@email.com',
            'phone' => $no_hp
        ],
        'item_details' => [
            [
                'id' => $pemesanan_id,
                'price' => (int)$nominal,
                'quantity' => 1,
                'name' => $nama_paket
            ]
        ]
    ];
    try {
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        // Simpan dulu "pending" ke DB biar ada record
        $sql = "INSERT INTO pembayaran (pemesanan_id, nominal, jenis, status, order_id, created_at)
                VALUES (:pemesanan_id, :nominal, :jenis, 'pending', :order_id, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':pemesanan_id' => $pemesanan_id,
            ':nominal' => $nominal,
            ':jenis' => $jenis,
            ':order_id' => $order_id
        ]);
        echo "
        <html>
        <head>
            <script src='https://app.sandbox.midtrans.com/snap/snap.js' data-client-key='SB-Mid-client-cZLZVu_nfCokKJgL'></script>
        </head>
        <body>
            <script type='text/javascript'>
                var baseurl = '$base_url';
                var jenis = '$jenis';
                snap.pay('$snapToken', {
                    onSuccess: function(result){
                        window.location.href ='success.php?order_id=' + result.order_id+'&jenis='+ jenis;
                    },
                    onPending: function(result){
                        alert('Menunggu pembayaran...');
                        window.location.href='pending.php?order_id=' + result.order_id;
                    },
                    onError: function(result){
                        alert('Pembayaran gagal');
                        console.log(result);
                    }
                });
            </script>
        </body>
        </html>";
    } catch (Exception $e) {
        echo "Midtrans Error: " . $e->getMessage();
    }
}

$pdo = null;

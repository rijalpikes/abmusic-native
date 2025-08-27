<?php
require_once '../midtrans_config.php';
include "../config/database.php";

// Ambil order_id dari URL
$order_id = $_GET['order_id'] ?? '';
$pdo = connect_db(); // Inisialisasi koneksi ke database
$transaction = null;
if ($order_id) {
    try {
        $transaction = \Midtrans\Transaction::status($order_id);
        // Ambil data user dari database berdasarkan order_id
        $stmt = $pdo->prepare("SELECT p.*, u.nama, u.no_hp, pk.nama_paket 
                               FROM pembayaran p
                               JOIN pemesanan pm ON p.pemesanan_id = pm.id
                               JOIN paket pk ON pm.paket_id = pk.id
                               WHERE p.order_id = :order_id LIMIT 1");
        $stmt->execute([':order_id' => $order_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $transaction->transaction_status == 'pending') {
            $nama   = $row['nama'];
            $no_hp  = $row['no_hp'];
            $paket  = $row['nama_paket'];
            $tarif_rupiah = "Rp " . number_format($transaction->gross_amount, 0, ',', '.');
            $payment_type = $transaction->payment_type;

            // Format pesan WhatsApp
            $pesan = "*Hayyy $nama*, status pembayaran kamu saat ini *PENDING*. ⏳\n\n" .
                "📦 *Paket*: $paket\n" .
                "💰 *Total*: $tarif_rupiah\n" .
                "💳 *Metode*: $payment_type\n" .
                "🆔 *Order ID*: $order_id\n\n" .
                "Silakan segera lakukan pembayaran sesuai instruksi agar pesananmu bisa diproses 🙏.\n\n" .
                "*ABmusic Team* 🎵";

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
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
// Ambil protocol
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Ambil domain (host)
$domain = $_SERVER['HTTP_HOST'];

// Bentuk base URL
$base_url = $protocol . $domain;
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Pembayaran Pending</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fdfdfd;
            text-align: center;
            padding: 40px;
        }

        .card {
            max-width: 500px;
            margin: auto;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #f39c12;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            border-radius: 6px;
            background: #3498db;
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>Status Pembayaran: PENDING</h2>

        <?php if ($transaction): ?>
            <p>Silakan lakukan pembayaran sesuai instruksi yang tersedia.</p>
            <table>
                <tr>
                    <td><b>Order ID</b></td>
                    <td><?= htmlspecialchars($transaction->order_id) ?></td>
                </tr>
                <tr>
                    <td><b>Status</b></td>
                    <td><?= htmlspecialchars($transaction->transaction_status) ?></td>
                </tr>
                <tr>
                    <td><b>Metode Bayar</b></td>
                    <td><?= htmlspecialchars($transaction->payment_type) ?></td>
                </tr>
                <tr>
                    <td><b>Total</b></td>
                    <td>Rp <?= number_format($transaction->gross_amount, 0, ',', '.') ?></td>
                </tr>
                <?php if (isset($transaction->va_numbers[0])): ?>
                    <tr>
                        <td><b>No Virtual Account</b></td>
                        <td><?= htmlspecialchars($transaction->va_numbers[0]->va_number) ?> (<?= htmlspecialchars($transaction->va_numbers[0]->bank) ?>)</td>
                    </tr>
                <?php endif; ?>
            </table>
        <?php elseif (isset($error)): ?>
            <p style="color:red;">Error: <?= htmlspecialchars($error) ?></p>
        <?php else: ?>
            <p>Order ID tidak ditemukan.</p>
        <?php endif; ?>

        <a class="btn" href=<?= $base_url; ?>>Kembali ke Beranda</a>
    </div>
</body>

</html>
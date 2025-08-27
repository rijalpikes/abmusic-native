<?php
require_once __DIR__ . '/../midtrans/Midtrans.php';
include "../config/database.php";
require_once '../midtrans_config.php';
include "../includes/fungsi.php";

$order_id = $_GET['order_id'] ?? null;
$jenis    = $_GET['jenis'];
$pdo = connect_db(); // Inisialisasi koneksi ke database
$status = null;
if ($order_id) {
    if ($jenis == 'tunai') {

        $transaction_status = 'settlement';
    } else {
        // Cek status transaksi dari Midtrans
        $status = \Midtrans\Transaction::status($order_id);

        $transaction_status = $status->transaction_status;
        $gross_amount = $status->gross_amount;
    }

    if ($transaction_status == 'settlement' || $transaction_status == 'capture') {
        // Ambil data dari database berdasarkan order_id
        $stmt = $pdo->prepare("SELECT p.*, pm.nama, pm.no_hp, pk.nama_paket, pm.tanggal_pesan, pm.tarif, p.jenis, sum(nominal) AS total_nominal
                               FROM pembayaran p
                               JOIN pemesanan pm ON p.pemesanan_id = pm.id
                               JOIN paket pk ON pm.paket = pk.id
                               WHERE p.order_id = :order_id LIMIT 1");
        $stmt->execute([':order_id' => $order_id]);
        $pembayaran = $stmt->fetch(PDO::FETCH_ASSOC);

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
                . "🙏 Kami sangat menghargai jika Anda bisa memberikan testimoni di link berikut:\n"
                . "👉 {$url_testimoni}\n\n"
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

        // Format nomor WA (pastikan sudah 62xxxx)
        $nomorWA = preg_replace('/^0/', '62', $pembayaran['no_hp']) . '@c.us';

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

        // Update status di DB
        $stmt = $pdo->prepare("UPDATE pembayaran SET status='success' WHERE order_id=:order_id");
        $stmt->execute([':order_id' => $order_id]);
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
                <?php if ($pembayaran): ?>

                    <?php if ($pembayaran['jenis'] == 'tunai'): ?>
                        <!-- ================== TAMPILAN UNTUK TUNAI ================== -->
                        <h2>Pembayaran Tunai</h2>
                        <table>
                            <tr>
                                <td><b>Kode</b></td>
                                <td><?= htmlspecialchars($pemesanan['kode_bayar']) ?></td>
                            </tr>
                            <tr>
                                <td><b>Status</b></td>
                                <td><?= htmlspecialchars($pembayaran['status']) ?></td>
                            </tr>
                            <tr>
                                <td><b>Total</b></td>
                                <td>Rp <?= number_format($pemesanan['tarif'], 0, ',', '.') ?></td>
                            </tr>
                            <?php if ($pemesanan['tarif'] != $pemesanan['total_nominal']):  ?>
                                <tr>
                                    <td><b>Pembayaran Panjar</b></td>
                                    <td>Rp <?= number_format($pemesanan['total_nominal'], 0, ',', '.') ?></td>
                                </tr>

                            <?php endif; ?>

                        </table>

                        <?php if ($pembayaran['status'] == 'success'): ?>
                            <?php if ($pemesanan['tarif'] == $pemesanan['total_nominal']): ?>
                                <p style="color:green;"><b>Pembayaran tunai sudah diterima (LUNAS).</b></p>
                            <?php elseif ($pemesanan['nominal'] > 0 && $pemesanan['nominal'] < $pemesanan['tarif']): ?>
                                <p style="color:orange;"><b>Pembayaran Panjar: sudah bayar sebagian (<?= number_format($pemesanan['total_nominal'], 0, ',', '.') ?>) namun belum lunas.</b></p>
                                <p>Sisa yang harus dibayar:
                                    Rp <?= number_format($pemesanan['tarif'] - $pemesanan['total_nominal'], 0, ',', '.') ?></p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p style="color:orange;"><b>Menunggu konfirmasi pembayaran tunai.</b></p>
                        <?php endif; ?>

                    <?php else: ?>
                        <!-- ================== TAMPILAN UNTUK NON TUNAI ================== -->
                        <h2>Pembayaran Non Tunai</h2>
                        <table>
                            <tr>
                                <td><b>Order ID</b></td>
                                <td><?= htmlspecialchars($status->order_id) ?></td>
                            </tr>
                            <tr>
                                <td><b>Status</b></td>
                                <td><?= htmlspecialchars($status->status_message) ?></td>
                            </tr>
                            <tr>
                                <td><b>Metode Bayar</b></td>
                                <td><?= htmlspecialchars(formatPaymentType($status->payment_type)) ?></td>
                            </tr>
                            <tr>
                                <td><b>Total</b></td>
                                <td>Rp <?= number_format($pembayaran['tarif'], 0, ',', '.') ?></td>
                            </tr>
                            <?php if ($pemesanan['tarif'] != $pemesanan['total_nominal']):  ?>
                                <tr>
                                    <td><b>Pembayaran Panjar</b></td>
                                    <td>Rp <?= number_format($pemesanan['total_nominal'], 0, ',', '.') ?></td>
                                </tr>

                            <?php endif; ?>
                        </table>
                        <?php if ($status->transaction_status == 'settlement'): ?>
                            <?php if ($pemesanan['tarif'] == $pemesanan['total_nominal']): ?>
                                <p style="color:green;"><b>Pembayaran tunai sudah diterima (LUNAS).</b></p>
                            <?php elseif ($pemesanan['nominal'] > 0 && $pemesanan['nominal'] < $pemesanan['tarif']): ?>
                                <p style="color:orange;"><b>Pembayaran Panjar: sudah bayar sebagian (<?= number_format($pemesanan['total_nominal'], 0, ',', '.') ?>) namun belum lunas.</b></p>
                                <p>Sisa yang harus dibayar:
                                    Rp <?= number_format($pemesanan['tarif'] - $pemesanan['total_nominal'], 0, ',', '.') ?></p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p style="color:orange;"><b>Menunggu konfirmasi pembayaran tunai.</b></p>
                        <?php endif; ?>
                    <?php endif; ?>

                <?php elseif (isset($error)): ?>
                    <p style="color:red;">Error: <?= htmlspecialchars($error) ?></p>
                <?php else: ?>
                    <p>Order ID tidak ditemukan.</p>
                <?php endif; ?>

                <a class="btn" href="<?= $base_url; ?>">Kembali ke Beranda</a>
            </div>
        </body>



        </html>
<?php
    } else {
        echo "Transaksi belum berhasil. Status: " . $transaction_status;
    }
} else {
    echo "Order ID tidak ditemukan.";
}

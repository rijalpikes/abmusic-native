<?php
include "../config/database.php";
// cek apakah ada input nomor
$kode = isset($_GET['kode']) ? $_GET['kode'] : null;
$detail = null;
$error = null;
$alreadyTestimoni = false;
if ($kode) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $kode = $_GET['kode'] ?? 0;
        $pdo = connect_db();

        // cek nomor di database
        $stmt = $pdo->prepare("
            SELECT 
                pemesanan.id AS pemesanan_id,
                pemesanan.nama,
                pemesanan.no_hp,
                pemesanan.tanggal_pesan,
                pemesanan.keterangan,
                paket.nama_paket,
                paket.harga_paket,
                pemesanan.tarif
            FROM pemesanan
            LEFT JOIN paket ON paket.id = pemesanan.paket
            WHERE pemesanan.kode_bayar = ? OR pemesanan.no_hp = ?
        ");
        $stmt->execute([$kode, $kode]);
        $detail = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($detail) {
            // cek apakah sudah ada testimoni
            $cekTestimoni = $pdo->prepare("SELECT id FROM testimoni WHERE pemesanan_id = ?");
            $cekTestimoni->execute([$detail['pemesanan_id']]);
            if ($cekTestimoni->fetch()) {
                $alreadyTestimoni = true;
            }

            // ambil total pembayaran
            $stmt2 = $pdo->prepare("SELECT SUM(nominal) as total_bayar FROM pembayaran WHERE pemesanan_id = ? AND status = ? ");
            $stmt2->execute([$detail['pemesanan_id'], 'success']);
            $bayar = $stmt2->fetch(PDO::FETCH_ASSOC);

            $total_bayar = $bayar['total_bayar'] ?? 0;
            $sisa_tagihan = $detail['harga_paket'] - $total_bayar;

            // simpan ke detail biar bisa dipakai di view
            $detail['total_bayar'] = $total_bayar;
            $detail['sisa_tagihan'] = $sisa_tagihan;
        } else {
            $error = "Kode tidak ditemukan dalam database!";
        }
    }
}


?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABmusic</title>
    <link rel="stylesheet" href="../assets/css/style_phone.css">
</head>

<body>
    <div class="phone-frame">
        <h2>Testimoni ABmusic</h2>
        <?php if (!$kode || !$detail): ?>
            <!-- Input nomor telepon -->
            <form method="get">
                <input type="text" name="kode" placeholder="Masukkan nomor telepon" required>
                <button type="submit" class="btn-submit">Cek Pemesanan</button>
            </form>
        <?php else: ?>
            <?php if ($error): ?>
                <p class="error"><?= $error ?></p>
            <?php elseif ($alreadyTestimoni): ?>
                <!-- Info -->
                <div class="flex items-start gap-2 bg-green-50 border border-green-200 text-green-700 p-3 rounded-lg mb-4">
                    <span class="text-xl">✅</span>
                    <p class="text-sm">
                        Terima kasih, Anda sudah pernah mengisi testimoni untuk pesanan ini.
                    </p>
                </div>

                <!-- Tombol -->
                <a href="/"
                    class="block w-full text-center bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
                    ⬅️ Kembali ke Awal
                </a>

            <?php else: ?>
                <div class="detail-container">
                    <form method="post" action="testimoni.php">
                        <input type="hidden" name="pemesanan_id" value="<?= $detail['pemesanan_id'] ?>">
                        <input type="hidden" name="nama" value="<?= $detail['nama'] ?>">
                        <input type="hidden" name="nama_paket" value="<?= $detail['nama_paket'] ?>">
                        <!-- <input type="hidden" name="tarif" value="<?= $detail['tarif'] ?>"> -->
                        <input type="hidden" name="no_hp" value="<?= $detail['no_hp'] ?>">
                        <!-- <input type="hidden" name="keterangan" value="<?= $detail['keterangan'] ?>"> -->
                        <!-- <input type="hidden" name="tanggal_pesan" value="<?= $detail['tanggal_pesan'] ?>"> -->
                        <!-- <input type="hidden" name="total_bayar" value="<?= $detail['total_bayar'] ?>"> -->
                        <!-- <input type="hidden" name="sisa_tagihan" value="<?= $detail['sisa_tagihan'] ?>"> -->

                        <div class="detail-card">
                            <p><strong>Nama:</strong> <?= htmlspecialchars($detail['nama']) ?></p>
                            <p><strong><?= htmlspecialchars($detail['nama_paket']) ?></strong> </p>
                            <!-- Rating Bintang -->
                            <label>Rating</label>
                            <div class="star-rating" id="star-rating">
                                <span class="star" data-value="1">&#9733;</span>
                                <span class="star" data-value="2">&#9733;</span>
                                <span class="star" data-value="3">&#9733;</span>
                                <span class="star" data-value="4">&#9733;</span>
                                <span class="star" data-value="5">&#9733;</span>
                            </div>
                            <input type="hidden" id="rating" name="rating" required>

                            <label for="pesan">Pesan</label>
                            <textarea id="pesan" name="pesan" rows="4"></textarea>

                            <button type="submit">Kirim Testimoni</button>
                        </div>
                    </form>
                </div>

            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>

<script>
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating');

    stars.forEach((star, index) => {
        star.addEventListener('click', () => {
            ratingInput.value = star.getAttribute('data-value');
            stars.forEach((s, i) => {
                s.classList.toggle('active', i < index + 1);
            });
        });

        star.addEventListener('mouseover', () => {
            stars.forEach((s, i) => {
                s.style.color = i <= index ? '#f97316' : '#ccc';
            });
        });

        star.addEventListener('mouseout', () => {
            stars.forEach((s, i) => {
                s.style.color = i < ratingInput.value ? '#f97316' : '#ccc';
            });
        });
    });

    document.getElementById('testimoniForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert("Terima kasih, testimoni Anda sudah terkirim!\n" +
            "Nama: " + document.getElementById('nama').value +
            "\nNo HP: " + document.getElementById('no_hp').value +
            "\nRating: " + ratingInput.value +
            "\nPesan: " + document.getElementById('pesan').value);
    });
</script>


</html>
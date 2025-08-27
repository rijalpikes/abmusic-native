<?php
include "../config/database.php";
// cek apakah ada input nomor
$kode = isset($_GET['kode']) ? $_GET['kode'] : null;
$detail = null;
$error = null;

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

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABmusic</title>
    <link rel="stylesheet" href="../assets/css/style_phone.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9fafb;
            padding: 30px;
        }

        .form-container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            color: #444;
            margin-bottom: 6px;
            display: block;
        }

        input,
        textarea {
            width: 95%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 16px;
            outline: none;
            transition: border 0.2s;
        }

        input:focus,
        textarea:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.3);
        }

        .star-rating {
            display: flex;
            gap: 8px;
            font-size: 28px;
            cursor: pointer;
            margin-bottom: 16px;
        }

        .star {
            color: #ccc;
            transition: color 0.2s;
        }

        .star.active,
        .star:hover,
        .star:hover~.star {
            color: #f97316;
        }

        button {
            width: 100%;
            background: #f97316;
            border: none;
            padding: 12px;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #ea580c;
        }
    </style>
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
            <?php endif; ?>

            <?php if ($detail): ?>
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
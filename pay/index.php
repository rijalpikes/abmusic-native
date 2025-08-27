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
        $sisa_tagihan = $detail['tarif'] - $total_bayar;
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
    <title>Pembayaran ABmusic</title>
    <link rel="stylesheet" href="../assets/css/style_phone.css">
</head>

<body>
    <div class="phone-frame">
        <h2>Pembayaran ABmusic</h2>
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
                    <form method="post" action="payment.php">
                        <input type="hidden" name="pemesanan_id" value="<?= $detail['pemesanan_id'] ?>">
                        <input type="hidden" name="nama" value="<?= $detail['nama'] ?>">
                        <input type="hidden" name="nama_paket" value="<?= $detail['nama_paket'] ?>">
                        <input type="hidden" name="tarif" value="<?= $detail['tarif'] ?>">
                        <input type="hidden" name="no_hp" value="<?= $detail['no_hp'] ?>">
                        <input type="hidden" name="keterangan" value="<?= $detail['keterangan'] ?>">
                        <input type="hidden" name="tanggal_pesan" value="<?= $detail['tanggal_pesan'] ?>">
                        <input type="hidden" name="total_bayar" value="<?= $detail['total_bayar'] ?>">
                        <input type="hidden" name="sisa_tagihan" value="<?= $detail['sisa_tagihan'] ?>">

                        <div class="detail-card">
                            <h2>Detail Pembayaran</h2>
                            <p><strong>Nama:</strong> <?= htmlspecialchars($detail['nama']) ?></p>
                            <p><strong><?= htmlspecialchars($detail['nama_paket']) ?></strong> </p>
                            <p class="tarif">Total: <span class="harga">Rp <?= number_format($detail['tarif'], 0, ',', '.') ?></span></p>
                            <?php if ($detail['total_bayar'] > 0): ?>
                                <p><b>Sudah Dibayar: Rp <?= number_format($detail['total_bayar'], 0, ',', '.') ?></b></p>
                                <p><b>Sisa Tagihan: Rp <?= number_format($detail['sisa_tagihan'], 0, ',', '.') ?></b></p>
                            <?php endif; ?>
                            <!-- Jenis Pembayaran (radio pill button) -->
                            <label><strong>Pilih Jenis Pembayaran:</strong></label>
                            <div class="payment-options">
                                <input type="radio" id="tunai" name="jenis" value="tunai" checked>
                                <label for="tunai">Tunai</label>

                                <input type="radio" id="non_tunai" name="jenis" value="non_tunai">
                                <label for="non_tunai">Non Tunai</label>
                            </div>

                            <!-- Form Input Nilai Bayar -->
                            <label for="nilai"><strong>Masukkan Nilai Bayar:</strong></label>
                            <input type="number" id="nominal" name="nominal" placeholder="Masukkan jumlah bayar">

                            <!-- Preview Nominal -->
                            <p id="previewNominal" class="preview">Total Bayar: Rp 0</p>
                            <!-- Tombol Submit -->
                            <button type="submit" id="btnBayar" class="btn" disabled>Bayar Sekarang</button>
                        </div>
                    </form>
                </div>

            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>

<!-- Midtrans Snap.js -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-server-n9Pk2tmX3iCuzb_myTQG8GKT"></script>

<script>
    const bayarInput = document.getElementById("nominal");
    const previewNominal = document.getElementById("previewNominal");
    const btnBayar = document.getElementById("btnBayar");
    const tarif = <?= $detail['sisa_tagihan'] ?>;
    const form = document.getElementById("formPembayaran");

    bayarInput.addEventListener("input", () => {
        let val = parseInt(bayarInput.value) || 0;
        previewNominal.innerText = "Total Bayar: Rp " + val.toLocaleString("id-ID");

        if (val > 0 && val <= tarif) {
            btnBayar.disabled = false;
            btnBayar.classList.add("active");
        } else {
            btnBayar.disabled = true;
            btnBayar.classList.remove("active");
        }
    });

    // handle submit
    form.addEventListener("submit", function(e) {
        const jenis = document.querySelector('input[name="jenis"]:checked').value;
        if (jenis === "non_tunai") {
            e.preventDefault(); // cegah submit biasa

            let formData = new FormData(form);
            fetch("get_snap_token.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.snapToken) {
                        snap.pay(data.snapToken, {
                            onSuccess: function(result) {
                                console.log("Sukses", result);
                            },
                            onPending: function(result) {
                                console.log("Pending", result);
                            },
                            onError: function(result) {
                                console.log("Error", result);
                            },
                            onClose: function() {
                                alert("Popup ditutup tanpa bayar");
                            }
                        });
                    } else {
                        alert("Gagal ambil snap token!");
                    }
                });
        }
        // kalau tunai, langsung submit ke simpan_pembayaran.php
    });
</script>


</html>
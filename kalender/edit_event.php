<?php
include "../config/database.php";
$pdo = connect_db();

// Ambil data event berdasarkan ID
if (!isset($_GET['id'])) {
    die("ID tidak ditemukan.");
}
$id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM pemesanan WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die("Event tidak ditemukan.");
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $tanggal = $_POST['tanggal_pesan'];
    $lokasi = $_POST['lokasi'];
    $tarif = $_POST['tarif'];
    $keterangan = $_POST['keterangan'];

    $stmt = $pdo->prepare("UPDATE pemesanan 
                           SET nama=?, tanggal_pesan=?, lokasi=?, tarif=?, keterangan=? 
                           WHERE id=?");
    $stmt->execute([$nama, $tanggal, $lokasi, $tarif, $keterangan, $id]);
    // Ambil protocol
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
        || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

    // Ambil domain (host)
    $domain = $_SERVER['HTTP_HOST'];

    // Bentuk base URL
    $base_url = $protocol . $domain;

    $url_kalender = $base_url . "/kalender/index.php?msg=updated";
    header("Location:" . $url_kalender); // balik ke kalender
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Biar responsive di HP -->
    <title>Edit Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f6fa;
        }

        .mobile-container {
            max-width: 500px;
            /* tampil seperti layar HP */
            margin: auto;
            padding: 15px;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <div class="mobile-container">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3 text-center">✏️ Edit Event</h4>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control"
                            value="<?= htmlspecialchars($event['nama']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal_pesan" class="form-control"
                            value="<?= $event['tanggal_pesan'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control"
                            value="<?= htmlspecialchars($event['lokasi']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tarif</label>
                        <input type="number" name="tarif" class="form-control"
                            value="<?= $event['tarif'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($event['keterangan']) ?></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                        <a href="kalender.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>
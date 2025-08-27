<?php
include "../config/database.php";

$pdo = connect_db(); // Inisialisasi koneksi ke database


if (isset($_GET['event_id'])) {
    $event_id = intval($_GET['event_id']);
    $stmt = $pdo->prepare("SELECT * FROM pembayaran WHERE pemesanan_id = ?");
    $stmt->execute([$event_id]);

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $row['nominal'] = "Rp " . number_format($row['nominal'], 0, ',', '.');
        // Contoh: Rp 1.500.000
        $data[] = $row;
    }
    echo json_encode($data);
}

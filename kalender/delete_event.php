<?php
include "../config/database.php";

$pdo = connect_db(); // koneksi PDO

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    try {
        // Mulai transaksi
        $pdo->beginTransaction();

        // Ambil semua file terkait event
        $stmt = $pdo->prepare("SELECT file_name FROM galeri_event WHERE id_event = ?");
        $stmt->execute([$id]);
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Hapus testimoni dulu
        $stmt = $pdo->prepare("DELETE FROM testimoni WHERE pemesanan_id = ?");
        $stmt->execute([$id]);

        // Hapus pembayaran
        $stmt = $pdo->prepare("DELETE FROM pembayaran WHERE pemesanan_id = ?");
        $stmt->execute([$id]);

        // Hapus galeri_event
        $stmt = $pdo->prepare("DELETE FROM galeri_event WHERE id_event = ?");
        $stmt->execute([$id]);

        // Hapus file fisik setelah query sukses
        foreach ($files as $row) {
            $file_name = $row['file_name'];
            $fullPath = __DIR__ . "/assets/galeri/" . $file_name; // sesuaikan folder upload kamu
            if (file_exists($fullPath)) {
                if (!unlink($fullPath)) {
                    $pdo->rollBack();
                    echo "Gagal menghapus file: " . htmlspecialchars($file_name);
                    exit;
                }
            }
        }

        // Terakhir hapus pemesanan
        $stmt = $pdo->prepare("DELETE FROM pemesanan WHERE id = ?");
        $stmt->execute([$id]);

        // Commit transaksi
        $pdo->commit();

        echo "✅ Event berhasil dihapus beserta file & data terkait!";
    } catch (\Throwable $e) {
        // Rollback kalau ada error
        $pdo->rollBack();
        echo "❌ Error: " . $e->getMessage();
    }
}

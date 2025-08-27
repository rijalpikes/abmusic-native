<?php
include "../config/database.php";

$pdo = connect_db(); // Inisialisasi koneksi ke database
$id_event = $_GET['event_id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM galeri_event WHERE id_event = ?");
$stmt->execute([$id_event]);
$data = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $ext = strtolower(pathinfo($row['file_name'], PATHINFO_EXTENSION));
    $type = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'image' : 'video';
    $data[] = [
        'file_name' => $row['file_name'],
        'type' => $type
    ];
}
echo json_encode($data);

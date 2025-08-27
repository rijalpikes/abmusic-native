<?php
$targetDir = "../assets/galeri/";
include "../config/database.php";

$pdo = connect_db(); // Inisialisasi koneksi ke database

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Cek apakah ada file yang diupload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
    $event_id = $_POST['event_id'] ?? 0;
    $files = $_FILES['images'];

    $response = [];

    if ($event_id > 0) {
        for ($i = 0; $i < count($files['name']); $i++) {
            $fileName = $event_id . "_" . time() . "_" . basename($files['name'][$i]);
            $targetPath = $targetDir . $fileName;

            $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));

            // Cek tipe file
            if (in_array($fileType, ["jpg", "jpeg", "png", "gif"])) {
                $type = "image";
            } elseif (in_array($fileType, ["mp4", "mov", "avi", "webm"])) {
                $type = "video";
            } else {
                echo json_encode(["status" => "error", "msg" => "File tidak didukung"]);
                exit;
            }


            if (move_uploaded_file($files['tmp_name'][$i], $targetPath)) {
                // Simpan ke database
                $stmt = $pdo->prepare("INSERT INTO galeri_event (id_event, file_name, type, created_at) VALUES (?, ?, ?, ?)");
                $stmt->execute([$event_id, $fileName, $type, date('Y-m-d H:i:s')]);

                $response[] = [
                    'status' => 'success',
                    'file' => $fileName
                ];
            } else {
                $response[] = [
                    'status' => 'error',
                    'file' => $files['name'][$i]
                ];
            }
        }
    } else {
        $response[] = ['status' => 'error', 'message' => 'ID Event tidak valid'];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

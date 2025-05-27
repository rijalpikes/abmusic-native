<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Kalender Event - ABmusic</title>
</head>

<body>
    <h2>Kalender Event ABmusic</h2>
    <table border="1" cellpadding="5">
        <tr>
            <th>Nama Event</th>
            <th>Tanggal</th>
            <th>Deskripsi</th>
        </tr>
        <?php
    $result = $koneksi->query("SELECT * FROM kalender_event ORDER BY tanggal_event ASC");
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['nama_event']}</td><td>{$row['tanggal_event']}</td><td>{$row['deskripsi']}</td></tr>";
    }
    ?>
    </table>
    <br><a href="index.php">Kembali ke Beranda</a>
</body>

</html>
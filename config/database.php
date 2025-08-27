<?php
function connect_db() {
    $host = 'localhost';
    $port = '3306';
    $db = 'abmusic_db';
    $user = 'root';
    $pass = '';
    // $host = '82.112.234.76';
    // $port = '3307';
    // $db = 'abmusic_db';
    // $user = 'mysql';
    // $pass = '90b2ba6495ad2a377087';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        die('Koneksi gagal: ' . $e->getMessage());
    }
}
?>
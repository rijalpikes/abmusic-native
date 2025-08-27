<?php
require_once __DIR__ . '/midtrans/Midtrans.php';

// Set konfigurasi Midtrans
// \Midtrans\Config::$serverKey = 'Mid-client-sX0jXmAql8F26m8Q';
// \Midtrans\Config::$isProduction = true;
// \Midtrans\Config::$isSanitized = true;
// \Midtrans\Config::$is3ds = true;


// Sandbox
\Midtrans\Config::$serverKey = 'SB-Mid-server-n9Pk2tmX3iCuzb_myTQG8GKT';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

<?php

function formatTanggalIndo($tanggal)
{
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];
    $pecah = explode('-', $tanggal);
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
}


function formatPaymentType($string)
{
    if (!$string) return '';

    // Ubah ke huruf kecil semua
    $string = strtolower($string);

    // Ganti underscore jadi spasi
    $string = str_replace("_", " ", $string);

    // Kapital di awal kata
    return ucwords($string);
}
